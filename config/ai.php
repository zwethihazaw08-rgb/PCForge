<?php

// Set these in the Apache/PHP environment, never in browser code or source control.
return [
    'key' => trim((string) getenv('GROQ_API_KEY')),
    'model' => getenv('GROQ_MODEL') ?: 'openai/gpt-oss-20b',
];
