# Laravel SSP-INIT

SSP-INIT is a Laravel package designed to facilitate server-side processing (SSP) for Eloquent models, supporting dynamic filtering, sorting, and pagination. This package simplifies the development of data-intensive applications by abstracting complex server-side operations.

## Features

- **Dynamic Filtering**: Apply filters based on request parameters.
- **Sorting**: Sort data by any model attribute.
- **Pagination**: Efficiently paginate the data based on the client's request.
- **Eager Loading**: Supports eager loading of relationships to optimize query performance.
- **Response Encoding**: Optionally encode the response data.

## Requirements

- PHP ^8.2
- Laravel ^8.0

## Installation

To install the SSP-INIT package, run the following command in the root of your Laravel project:

```bash
composer require mrkacmaz/laravel-ssp
```

## Usage
Here is a basic example of how to use the SSP trait in a Laravel controller:

### Controller
```php
<?php

namespace App\Http\Controllers;

use Mrkacmaz\LaravelSsp\Traits\SSP;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use SSP;

    public function index(Request $request)
    {
        if ($request->expectsJson()){
            $data = self::processSSP($request, User::class);
            return response()->json($data);
        }
        return view('welcome');
    }
}
```

### Blade File
```blade.php
<x-app-layout>
    <table class="table table-hover">
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Full Name</th>
            <th scope="col">Email</th>
        </tr>
        </thead>
        <tbody></tbody>
    </table>
</x-app-layout>
```

### JS File
```js
$(function (){
    $('.table').DataTable({
        serverSide: true,
        processing: true,
        ajax: {
            url: window.location,
            type: 'GET',
            dataSrc: function (data) {
                return JSON.parse(atob(data.data));
            }
        },
        columns: [
            {data: 'id'},
            {data: 'name'},
            {data: 'email'},
        ],
    });
});
```
In this example, the UserController utilizes the SSP trait to process data for the User model according to the parameters specified in the request.

## Configuration
No additional configuration is required to start using SSP-INIT, as it uses Laravel's default configurations.

## Support
If you are having issues, please let us know by [submitting an issue on GitHub.](https://github.com/mrkacmaz/laravel-ssp/issues)

## License
This project is licensed under the MIT License - see the [LICENSE.md](https://github.com/MrKacmaz/laravel-ssp/blob/master/LICENSE) file for details.

