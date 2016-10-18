PHP Reflection
==============
[![Build Status](https://travis-ci.org/ovr/phpreflection.svg?branch=master)](https://travis-ci.org/ovr/phpreflection)

# Function reflection

You can get function reflection by using `getFunction` from `$reflector`:

```php
$reflector = new Reflector(Reflector::manuallyFactory());
$reflection = $reflector->getFunction('gettype');
```

Next you can use methods from `FunctionReflection`, for example:

```php
var_dump($reflection->getNumberOfParameters());
var_dump($reflection->getNumberOfRequiredParameters());
```

At all, you can run `function` from `$reflection`

```php
var_dump($reflection->run(array(1))); //float
```
