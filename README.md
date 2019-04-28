
[![Build Status](https://travis-ci.com/lbacik/value-object-illuminate-validation.svg?branch=master)](https://travis-ci.com/lbacik/value-object-illuminate-validation)

Validator wrapper (to be used with ValueObject) for Illuminate Validation class

ValueObject implementation: https://github.com/lbacik/value-object
Laravel documentation about using Laravel's validator features: https://laravel.com/docs/5.7/validation

Example value object declaration:

    class ExampleValueObject extends ValueObject 
    {
        protected $validators = [
            KeysValidator::class,
            IlluminateValidationValidator::class,
        ];
        
        protected $keys = [
            'id' => 'required|integer|min:1',
            'name' => 'required|string|max:5',
            'desc' => 'nullable|string|max:100',
        ];
    }

Value object creation and (instant) validation:

    $vo = new ExampleValueObject([
        'id' => 1,
        'name' => 'Ala',
        'desc' => 'Sample description',
    ]) 

For more examples please check the project's tests!
