<?php
define('LARAVEL_START', microtime(true));
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Role;

$roles = [
    'admin' => 'Admin Role',
    'staff' => 'Staff Role',
    'hr' => 'HR Role',
    'delivery_agent' => 'Delivery Agent Role'
];

foreach ($roles as $slug => $name) {
    try {
        $role = Role::updateOrCreate(
            ['slug' => $slug],
            [
                'name' => $name,
                'description' => "Default $name for the system",
                'permissions' => ($slug === 'admin' ? array_keys(Role::getAvailablePermissions()) : [])
            ]
        );
        echo "Successfully processed Role: $name ($slug)\n";
    } catch (\Exception $e) {
        echo "Error creating $slug: " . $e->getMessage() . "\n";
    }
}
echo "Seeding completed.\n";
