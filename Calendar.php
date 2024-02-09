<?php

namespace common\components;

use Exception;
use Yii;
use yii\base\Component;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Calendar;
use Google_Service_Calendar_CalendarListEntry;
use Google_Service_Calendar_AclRule;
use Google_Service_Calendar_AclRuleScope;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventReminder;
use Google_Service_Calendar_EventReminders;
use Google_Service_Calendar_EventAttendee;
use Google_Service_Calendar_ColorDefinition;
use Google_Service_Exception;

class Calendar extends Component
{
    private $key = '{
      "type": "service_account",
      "project_id": "****",
      "private_key_id": "****",
      "private_key": "-----BEGIN PRIVATE KEY-----\n*********-----\n",
      "client_email": "*****",
      "client_id": "*****",
      "auth_uri": "https://accounts.google.com/o/oauth2/auth",
      "token_uri": "https://oauth2.googleapis.com/token",
      "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
      "client_x509_cert_url": "****"
    }';
    public $googleClient;
    public $CalendarService;
    public $ApplicationName = '***********';
    public $TimeZone = 'America/Argentina/Buenos_Aires';
    // public $Subject = 'contacto@docdoc.com.ar';

    /**
     * Inicializa el cliente de Google Calendar
     *
     */
    public function __construct($Subject = '')
    {
        $this->googleClient = new Google_Client();

        $this->googleClient->setApplicationName($ApplicationName);
        $this->googleClient->setAuthConfig(json_decode($this->key, true));
        $this->googleClient->setScopes(Google_Service_Calendar::CALENDAR);
        if (!empty($Subject)) {
          $this->googleClient->setSubject($Subject);
        }
        
        $this->CalendarService = new Google_Service_Calendar($this->googleClient);

        parent::__construct();
    }

    public function insertAcl($calendarId, $scopeValue = '', $scopeType = 'user', $role = 'writer')
    {
      $rule = new Google_Service_Calendar_AclRule();
      $scope = new Google_Service_Calendar_AclRuleScope();

      $scope->setType($scopeType);
      $scope->setValue($scopeValue);

      $rule->setScope($scope);
      $rule->setRole($role);

      $createdRule = $this->CalendarService->acl->insert($calendarId, $rule);

      return $createdRule->getId();
    }

    public function updateAcl($calendarId, $ruleId, $role)
    {
      $rule = $this->CalendarService->acl->get($calendarId, $ruleId);
      $rule->setRole($role);

      $updatedRule = $this->CalendarService->acl->update($calendarId, $rule->getId(), $rule);

      return $updatedRule->getId();
    }

    public function deleteAcl($calendarId, $ruleId)
    {
      $deletedRule = $this->CalendarService->acl->delete($calendarId, $ruleId);

      return $deletedRule;
    }

    public function insertCalendar($summary, $description = '', $colorId = '', $location = '')
    {
        $calendar = new Google_Service_Calendar_Calendar();

        $calendar->setSummary($summary);
        $calendar->setDescription($description);
        $calendar->setLocation($location);
        $calendar->setTimeZone($this->TimeZone);

        $createdCalendar = $this->CalendarService->calendars->insert($calendar);

        if (!empty($colorId)) {
          $calendarListEntry = new Google_Service_Calendar_CalendarListEntry();
          $calendarListEntry->setId($createdCalendar->getId());
          $calendarListEntry->setColorId($colorId);

          $createdCalendarListEntry = $this->CalendarService->calendarList->insert($calendarListEntry);
        }

        return $createdCalendar->getId();
    }

    public function updateCalendar($calendarId, $newSummary = null, $newDescription = null, $newColorId = null, $newLocation = null, $newTimeZone = null)
    {
        $calendar = $this->CalendarService->calendars->get($calendarId);

        $oldSummary = $calendar->getSummary();
        $oldDescription = $calendar->getDescription();
        $oldLocation = $calendar->getLocation();
        $oldTimeZone = $calendar->getTimeZone();

        $calendar->setSummary($newSummary ?? $oldSummary);
        $calendar->setDescription($newDescription ?? $oldDescription);
        $calendar->setLocation($newLocation ?? $oldLocation);
        $calendar->setTimeZone($newTimeZone ?? $oldTimeZone);

        if ($newColorId !== null) {
          $calendarListEntry = $this->CalendarService->calendarList->get($calendarId);
          $calendarListEntry->setColorId($newColorId);

          $updatedCalendarListEntry = $this->CalendarService->calendarList->update($calendarListEntry->getId(), $calendarListEntry);
        }

        $updateCalendar = $this->CalendarService->calendars->update($calendarId, $calendar);

        return $updateCalendar->getId();
    }

    public function deleteCalendar($calendarId) 
    {
      $deletedCalendar = $this->CalendarService->calendars->delete($calendarId);

      return $deletedCalendar;
    }

    public function insertEvent($Summary = '', $Description = '', $Attendees = array(), $start, $end, $calendarId, $colorId = '', $location = '')
    {
      $event = new Google_Service_Calendar_Event(array(
          'summary' => $Summary,
          'location' => $location,
          'description' => $Description,
          'start' => array(
            'dateTime' => $start,
            'timeZone' => $this->TimeZone,
          ),
          'end' => array(
            'dateTime' => $end,
            'timeZone' => $this->TimeZone,
          )
      ));

      if (!empty($colorId)) {
        $event->setColorId($colorId);
      }

      $remindersArray = array();

      $reminder = new Google_Service_Calendar_EventReminder();
      $reminder->setMethod('email');
      $reminder->setMinutes(24*60);
      $remindersArray[] = $reminder;
      
      $reminder = new Google_Service_Calendar_EventReminder();
      $reminder->setMethod('popup');
      $reminder->setMinutes(30);
      $remindersArray[] = $reminder;
      
      $reminders = new Google_Service_Calendar_EventReminders();
      $reminders->setUseDefault(false);
      $reminders->setOverrides($remindersArray);

      $event->setReminders($reminders);
      
      $attendees = array();
      
      foreach ($Attendees as $a) {
        $attendee = new Google_Service_Calendar_EventAttendee();
        $attendee->setEmail($a);
        $attendees[] = $attendee;
      }

      $event->attendees = $attendees;
      
      $createdEvent = $this->CalendarService->events->insert($calendarId, $event, array('sendNotifications' => true));

      return [
        'eventId' => $createdEvent->getId(),
        'eventLink' => $createdEvent->getHtmlLink()
      ];
    }

    public function updateEvent($eventId, $calendarId, $newSummary = null, $newDescription = null, $newStart = null, $newEnd = null, $newColorId = null, $newLocation = null, $newMinutesEmail = null, $newMinutesPopup = null, $newTimeZone = null)
    {
      $event = $this->CalendarService->events->get($calendarId, $eventId);

      $oldSummary = $event->getSummary();
      $oldDescription = $event->getDescription();
      $oldStart = $event->getStart()->dateTime;
      $oldEnd = $event->getEnd()->dateTime;
      $oldColorId = $event->getColorId();
      $oldLocation = $event->getLocation();
      $oldTimeZone = $event->getStart()->timeZone;

      $event->setSummary($newSummary ?? $oldSummary);
      $event->setDescription($newDescription ?? $oldDescription);
      $event->setStart(array(
        'dateTime' => $newStart ?? $oldStart,
        'timeZone' => $newTimeZone ?? $oldTimeZone,
      ));
      $event->setEnd(array(
        'dateTime' => $newEnd ?? $oldEnd,
        'timeZone' => $newTimeZone ?? $oldTimeZone,
      ));
      $event->setColorId($newColorId ?? $oldColorId);
      $event->setLocation($newLocation ?? $oldLocation);

      $remindersArray = array();

      if ($newMinutesEmail !== null) {
        $reminder = new Google_Service_Calendar_EventReminder();
        $reminder->setMethod('email');
        $reminder->setMinutes($newMinutesEmail);
        $remindersArray[] = $reminder;
      }
      
      if ($newMinutesPopup !== null) {
        $reminder = new Google_Service_Calendar_EventReminder();
        $reminder->setMethod('popup');
        $reminder->setMinutes($newMinutesPopup);
        $remindersArray[] = $reminder;
      }
      
      if (!empty($remindersArray)) {
        $reminders = new Google_Service_Calendar_EventReminders();
        $reminders->setUseDefault(false);
        $reminders->setOverrides($remindersArray);

        $event->setReminders($reminders);
      }
      
      $updatedEvent = $this->CalendarService->events->insert($calendarId, $eventId, $event, array('sendNotifications' => true));

      return [
        'eventId' => $updatedEvent->getId(),
        'eventLink' => $updatedEvent->getHtmlLink()
      ];
    }

    public function deleteEvent($calendarId, $eventId)
    {
      $deletedEvent = $this->CalendarService->events->delete($calendarId, $eventId);

      return $deletedEvent;
    }

    public function getColors()
    {
      $colors = $this->CalendarService->colors->get();

      return [
        'calendar' => $colors->getCalendar(),
        'event' => $colors->getEvent()
      ];
    }
}
