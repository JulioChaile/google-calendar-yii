# google-calendar-yii

# Yii2 Google Calendar Component

- [Español](#español)
- [English](#english)

# Español

## Índice

1. [Descripción](#descripción)
2. [Instalación](#instalación)
3. [Configuración](#configuración)
4. [Uso](#uso)
5. [Funciones](#funciones)
6. [Ejemplos](#ejemplos)
7. [Contribución](#contribución)
8. [Licencia](#licencia)

## Descripción

El componente Yii2 Google Calendar es un componente Yii2 para integrar con la API de Google Calendar.

## Instalación

Puedes instalar este componente a través de Composer. Ejecuta el siguiente comando en tu terminal:

```
composer require google/apiclient:"^2.0"
```

## Configuración

Para utilizar este componente, necesitas obtener las credenciales necesarias desde la Consola de Desarrolladores de Google.

1. Ve a [Google Developer Console](https://console.developers.google.com/).
2. Crea un nuevo proyecto o selecciona uno existente.
3. Habilita la API de Google Calendar para tu proyecto.
4. Crea credenciales para una cuenta de servicio.
5. Descarga el archivo JSON que contiene la clave de tu cuenta de servicio.

## Uso

Para utilizar el componente, necesitas configurarlo con las credenciales obtenidas y luego puedes realizar varias operaciones en Google Calendar como crear, actualizar o eliminar eventos y calendarios.

```php
use common\components\Calendar;

// Inicializa el componente de Calendar
$calendar = new Calendar();

// Usa las funciones del componente
$calendarId = $calendar->insertCalendar('Nuevo Calendario', 'Descripción', 'colorId', 'Ubicación');
```

## Funciones

Este componente proporciona las siguientes funciones:

- `insertAcl($calendarId, $scopeValue = '', $scopeType = 'user', $role = 'writer')`: Inserta un permiso ACL en un calendario. Retorna el ID del permiso creado.
- `updateAcl($calendarId, $ruleId, $role)`: Actualiza un permiso ACL en un calendario. Retorna el ID del permiso actualizado.
- `deleteAcl($calendarId, $ruleId)`: Elimina un permiso ACL de un calendario. Retorna el resultado de la operación de eliminación.
- `insertCalendar($summary, $description = '', $colorId = '', $location = '')`: Inserta un nuevo calendario. Retorna el ID del calendario creado.
- `updateCalendar($calendarId, $newSummary = null, $newDescription = null, $newColorId = null, $newLocation = null, $newTimeZone = null)`: Actualiza un calendario existente. Retorna el ID del calendario actualizado.
- `deleteCalendar($calendarId)`: Elimina un calendario. Retorna el resultado de la operación de eliminación.
- `insertEvent($Summary = '', $Description = '', $Attendees = array(), $start, $end, $calendarId, $colorId = '', $location = '')`: Inserta un nuevo evento en un calendario. Retorna un array con el ID del evento creado y el enlace al evento.
- `updateEvent($eventId, $calendarId, $newSummary = null, $newDescription = null, $newStart = null, $newEnd = null, $newColorId = null, $newLocation = null, $newMinutesEmail = null, $newMinutesPopup = null, $newTimeZone = null)`: Actualiza un evento existente en un calendario. Retorna un array con el ID del evento actualizado y el enlace al evento.
- `deleteEvent($calendarId, $eventId)`: Elimina un evento de un calendario. Retorna el resultado de la operación de eliminación.
- `getColors()`: Obtiene los colores disponibles para los calendarios y eventos. Retorna un array con los colores de calendario y evento disponibles.

## Ejemplos

```php
use common\components\Calendar;

// Inicializa el componente de Calendar
$calendar = new Calendar();

// Inserta un nuevo calendario
$calendarId = $calendar->insertCalendar('Nuevo Calendario', 'Descripción', 'colorId', 'Ubicación');

// Inserta un nuevo evento
$event = $calendar->insertEvent('Resumen del Evento', 'Descripción del Evento', ['asistente1@example.com'], '2024-02-09T10:00:00', '2024-02-09T12:00:00', $calendarId);
```

## Contribución

¡Las contribuciones son bienvenidas! Siéntete libre de abrir problemas o enviar solicitudes de extracción.

---

# English

## Index

1. [Description](#description)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [Usage](#usage)
5. [Functions](#functions)
6. [Examples](#examples)
7. [Contribution](#contribution)
8. [License](#license)

## Description

The Yii2 Google Calendar Component is a Yii2 component for integrating with the Google Calendar API.

## Installation

You can install this component via Composer. Run the following command in your terminal:

```
composer require google/apiclient:"^2.0"
```

## Configuration

To use this component, you need to obtain the necessary credentials from the Google Developer Console.

1. Go to [Google Developer Console](https://console.developers.google.com/).
2. Create a new project or select an existing one.
3. Enable the Google Calendar API for your project.
4. Create credentials for a service account.
5. Download the JSON file containing your service account key.

## Usage

To use the component, you need to configure it with the obtained credentials and then you can perform various operations on Google Calendar such as creating, updating, or deleting events and calendars.

```php
use common\components\Calendar;

// Initialize the Calendar component
$calendar = new Calendar();

// Use the component's functions
$calendarId = $calendar->insertCalendar('New Calendar', 'Description', 'colorId', 'Location');
```

## Functions

This component provides the following functions:

- `insertAcl($calendarId, $scopeValue = '', $scopeType = 'user', $role = 'writer')`: Inserts an ACL permission in a calendar. Returns the ID of the created permission.
- `updateAcl($calendarId, $ruleId, $role)`: Updates an ACL permission in a calendar. Returns the ID of the updated permission.
- `deleteAcl($calendarId, $ruleId)`: Deletes an ACL permission from a calendar. Returns the result of the deletion operation.
- `insertCalendar($summary, $description = '', $colorId = '', $location = '')`: Inserts a new calendar. Returns the ID of the created calendar.
- `updateCalendar($calendarId, $newSummary = null, $newDescription = null, $newColorId = null, $newLocation = null, $newTimeZone = null)`: Updates an existing calendar. Returns the ID of the updated calendar.
- `deleteCalendar($calendarId)`: Deletes a calendar. Returns the result of the deletion operation.
- `insertEvent($Summary = '', $Description = '', $Attendees = array(), $start, $end, $calendarId, $colorId = '', $location = '')`: Inserts a new event into a calendar. Returns an array with the ID of the created event and the event link.
- `updateEvent($eventId, $calendarId, $newSummary = null, $newDescription = null, $newStart = null, $newEnd = null, $newColorId = null, $newLocation = null, $newMinutesEmail = null, $newMinutesPopup = null, $newTimeZone = null)`: Updates an existing event in a calendar. Returns an array with the ID of the updated event and the event link.
- `deleteEvent($calendarId, $eventId)`: Deletes an event from a calendar. Returns the result of the deletion operation.
- `getColors()`: Gets the available colors for calendars and events. Returns an array with the available calendar and event colors.

## Examples

```php
use common\components\Calendar;

// Initialize the Calendar component
$calendar = new Calendar();

// Insert a new calendar
$calendarId = $calendar->insertCalendar('New Calendar', 'Description', 'colorId', 'Location');

// Insert a new event
$event = $calendar->insertEvent('Event Summary', 'Event Description', ['attendee1@example.com'], '2024-02-09T10:00:00', '2024-02-09T12:00:00', $calendarId);
```

## Contribution

Contributions are welcome! Feel free to open issues or send pull requests.
