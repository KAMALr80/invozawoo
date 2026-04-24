<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\Role;

$roles = [
    'staff' => 'Staff Role',
    'hr' => 'HR Role',
    'delivery_agent' => 'Delivery Agent Role'
];

foreach ($roles as $slug => $name) {
    Role::firstOrCreate(
        ['slug' => $slug],
        [
            'name' => $name,
            'description' => "Default $name for the system",
            'permissions' => []
        ]
    );
    echo "Created/Verified Role: $name ($slug)\n";
}
echo "Done!\n";
