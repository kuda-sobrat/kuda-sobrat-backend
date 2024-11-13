Анализ текста на наличие мероприятий. Верни JSON с полями:
- `is_event`
- `events` (массив с полями: `name`, `description`, `start_datetime` (ISO 8601, текущий год: 2024, месяц: 11), `end_datetime` или `null`, `location`, `is_paid`, `price` или `null`).

Текущий год: {{ $nowYear }} и месяц: {{ $nowMonth }}
Только JSON без дополнительного текста.

Текст:
{!! $inputText !!}
