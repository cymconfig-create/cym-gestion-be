<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$db = App\Ia\Mongo\MongoClientFactory::database();

echo "== Creating indexes ==\n";

$users = $db->selectCollection('users');
$users->createIndex(['user_id' => 1], ['unique' => true, 'name' => 'users_user_id_unique']);
$users->createIndex(['name' => 1], ['unique' => true, 'name' => 'users_name_unique']);
$users->createIndex(
    ['email' => 1],
    [
        'unique' => true,
        'name' => 'users_email_unique',
        'partialFilterExpression' => ['email' => ['$type' => 'string']],
    ]
);
$users->createIndex(['profile_id' => 1], ['name' => 'users_profile_id_idx']);

$profiles = $db->selectCollection('profiles');
$profiles->createIndex(['profile_id' => 1], ['unique' => true, 'name' => 'profiles_profile_id_unique']);
$profiles->createIndex(['code' => 1], ['unique' => true, 'name' => 'profiles_code_unique']);

$selectors = $db->selectCollection('selectors');
$selectors->createIndex(['selector_id' => 1], ['unique' => true, 'name' => 'selectors_selector_id_unique']);
$selectors->createIndex(['code' => 1], ['name' => 'selectors_code_idx']);
$selectors->createIndex(['dad_selector_code' => 1], ['name' => 'selectors_dad_selector_code_idx']);

$companies = $db->selectCollection('companies');
$companies->createIndex(['company_id' => 1], ['unique' => true, 'name' => 'companies_company_id_unique']);
$companies->createIndex(['nit' => 1], ['unique' => true, 'name' => 'companies_nit_unique']);
$companies->createIndex(['code' => 1], ['unique' => true, 'name' => 'companies_code_unique']);

$employees = $db->selectCollection('employees');
$employees->createIndex(['employee_id' => 1], ['unique' => true, 'name' => 'employees_employee_id_unique']);
$employees->createIndex(['identification_number' => 1], ['unique' => true, 'name' => 'employees_identification_number_unique']);
$employees->createIndex(['company_id' => 1], ['name' => 'employees_company_id_idx']);

$documents = $db->selectCollection('documents');
$documents->createIndex(['document_id' => 1], ['unique' => true, 'name' => 'documents_document_id_unique']);
$documents->createIndex(['code' => 1], ['unique' => true, 'name' => 'documents_code_unique']);

$attachments = $db->selectCollection('attachments');
$attachments->createIndex(['attachment_id' => 1], ['unique' => true, 'name' => 'attachments_attachment_id_unique']);
$attachments->createIndex(['company_id' => 1], ['name' => 'attachments_company_id_idx']);
$attachments->createIndex(['employee_id' => 1], ['name' => 'attachments_employee_id_idx']);
$attachments->createIndex(['document_id' => 1], ['name' => 'attachments_document_id_idx']);

$events = $db->selectCollection('events');
$events->createIndex(['id' => 1], ['unique' => true, 'name' => 'events_id_unique']);
$events->createIndex(['id_company' => 1], ['name' => 'events_id_company_idx']);

$conversations = $db->selectCollection('conversations');
$conversations->createIndex(['conversation_id' => 1], ['unique' => true, 'name' => 'conversations_conversation_id_unique']);
$conversations->createIndex(['created_by' => 1], ['name' => 'conversations_created_by_idx']);
$conversations->createIndex(['last_message_at' => -1], ['name' => 'conversations_last_message_at_desc_idx']);

$messages = $db->selectCollection('messages');
$messages->createIndex(['message_id' => 1], ['unique' => true, 'name' => 'messages_message_id_unique']);
$messages->createIndex(['conversation_id' => 1, 'created_at' => 1], ['name' => 'messages_conversation_created_at_idx']);

$pivot = $db->selectCollection('conversation_user');
$pivot->createIndex(['conversation_id' => 1, 'user_id' => 1], ['unique' => true, 'name' => 'conversation_user_conversation_user_unique']);
$pivot->createIndex(['user_id' => 1, 'is_deleted' => 1], ['name' => 'conversation_user_user_deleted_idx']);

echo "Indexes created/verified.\n";

echo "\n== Basic collection counts ==\n";
$collections = [
    'users',
    'profiles',
    'selectors',
    'companies',
    'employees',
    'documents',
    'attachments',
    'events',
    'conversations',
    'messages',
    'conversation_user',
];
foreach ($collections as $name) {
    $count = $db->selectCollection($name)->countDocuments();
    echo str_pad($name, 20) . " : " . $count . "\n";
}

echo "\nDone.\n";
