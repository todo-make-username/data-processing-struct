<a id="readme-top"></a>

<div align="center">

<!-- PROJECT SHIELDS -->
[![PHP][php-shield]][php-url]

[![Stargazers][stars-shield]][stars-url]
[![Issues][issues-shield]][issues-url]
[![License][license-shield]][license-url]

# Data Processing Struct

[Report Bug](https://github.com/todo-make-username/data-processing-struct/issues)
·
[Request Feature](https://github.com/todo-make-username/data-processing-struct/issues)

</div>

## Overview
**Overview [short version]:**\
You start with an array of data and class containing data processing property attributes. You end with a fully typed, processed, optionally immutable, validated object.

**Overview [long version]:**\
One main pain point for anyone working in PHP is processing and validating associative arrays that come from various sources ($_POST, PDO, json_decode, etc). Then we run into the repetitive task of having to revalidate that the data we want exists in the array and it is the correct type, every time we use that data in a new method (I mean, you don't have to, but it is safer that way). This can be nearly eliminated by passing around pre-processed data objects (like a struct in other languages) instead of arrays. You can enhance this even further by making the objects immutable with readonly properties. This library is how we load those arrays into objects while also processing and validating the data without all the boilerplate.

**There are three main actions this library was designed to help with:**
1. Hydration: Hydrate an object's public properties using an associative array of data.
	* Hydration attributes clean the incoming data, as can act as chainable setter methods that can use the incoming data to assign the object's property something different.
 	* Things like automatically running `trim`, or `str_replace` on a handful of properties only requires you to add the corresponding attribute to the desired properties on the object. 
	* For example, when the attribute `#[JsonDecode(true)]` is used on a property, it will expect a json string during hydration and then parses it. Then it uses that array in the next hydration attribute or saves it to the property.
		* That can then be chained with a custom attribute to take the array data and hydrate a different data object to be saved to the property. With just those 2 attributes you removed a lot of processing from your main flow.
1. Conversion: While hydrating an object, the values from the incoming data will be automatically converted to the property's type if it can right before assignment.
1. Validation: Validate an object's properties using validation attributes.
	* For example, you can set up an attribute that checks if the value of a property matches a regex pattern, or that the value must pass an `!empty` check.
 	* You can also customize the failure messages for the individual validation attributes on each property.

#### Common Use Cases:
* Processing and validating form data.
* Simple DB Mappers.
* API responses.
* Basically anything that has an array that would be better off as a typed object.

Did I mention that this library is fully extendable? You don't need to use any of my pre-made attributes. You can easily add your own hydration/validation attributes. As long as they extend my base attribute classes, the helpers will automatically pick up on them.

<p align="right">(<a href="#readme-top">back to top</a>)</p>

## Requirements
* PHP >= 8.2

Yup, that's it. Since this doesn't do anything fancy and mostly relies on built-in PHP features, no need for any external libraries for now.

The dev requirements are just the typical phpunit and php stan, and code sniffer.

<p align="right">(<a href="#readme-top">back to top</a>)</p>

## Installation
**Quick Note:** It is not currently set up as a composer package in packagist, I like pulling from github directly in my stuff. If a feature request is made in Github Issues, not by me, to add it as a composer package, I'll look into setting that up.

To install via composer, you need to have it look at this repo directly by modifying your `composer.json`. To do this, add the repo information in the `repositories` section with your desired version number or branch (the example shows using the main branch), or add the section if it doesn't exist. Then add the "package" to your `require` section. Then lastly run `composer update`.


composer.json
```
"repositories": [
    {
        "url": "https://github.com/todo-make-username/data-processing-struct.git",
        "type": "git"
    }
],
"require": {
    "todo-make-username/data-processing-struct": "dev-main"
},
```

<p align="right">(<a href="#readme-top">back to top</a>)</p>

## Quick Example of Usage:
In this example we will look at $_POST data. When it comes to arrays, the data doesn't follow strict guidelines which causes bugs. This library simplifies the validation and data processing for you. Below, you'll see I create an object which has properties that correspond with the expected $_POST keys. By moving away from arrays and towards data driven objects, we can create cleaner code which produces fewer bugs.

Here is a quick and dirty example of how it can be used on $_POST data after a form submission to prepare the data.
```PHP
/**
 * We will be pretending this is coming from a form for a product review.
 *
 * This example uses readonly properties to make the data immutable, but you don't have to use readonly.
 *
 * This class has properties that match what we are expecting $_POST to contain.
 * The exception being the file upload one, which looks at $_FILES instead.
 */
class ReviewFormData extends Struct
{
	#[Trim]     // Hydrator Attribute
	#[NotEmpty] // Validation Attribute
	public readonly string $name;

	// For this example, this is a checkbox, and therefore must have a default value if it is unchecked.
	// Readonly properties cannot have a default, so use the UseValueOnEmpty attribute
	#[UseValueOnEmpty(false)] // Hydrator Attribute
	public readonly bool $is_public_review;

	public readonly int $star_rating;

	#[StrReplace('*cat sitting on spacebar*', '')] // Hydrator Attribute
	#[Trim]                                        // Hydrator Attribute
	#[UseValueOnEmpty(null)]                       // Hydrator Attribute
	public readonly ?string $review_text;

	// FileUpload is a hydration attribute that pulls the data automatically from the $_FILES array.
	//		the optional param will format the array into a cleaner format for multi-uploads.
	#[FileUpload(transpose: true)] // Hydration Attribute
	public readonly array $review_image_uploads;
}

...

// This is the data we got from the form:
// is_public_review was a checkbox. For this example it was unchecked, which doesn't come through.
$_POST = [
	'name' => 'Nonya',
	'review_text' => 'I liked this product.       *cat sitting on spacebar*             ',
	'star_rating' => '4',
];

$_FILES = [
	'review_image_uploads' => [
		// This example will say there are 2 files.
	]
];

...

// Now somewhere else in the codebase where the form data is processed.
$FormObject = new ReviewFormData();

// The data from $_POST and $_FILES are pre-processed, typed, then set to the object's respective properties.
$FormObject->hydrate($_POST);

// Hydration can also be done by passing the array into the Struct constructor.
$FormObject = new ReviewFormData($_POST);

// Validation is run. Any failure messages can be retrieved with getMessages.
$Response         = $FormObject->validate();
$is_valid         = $Response->success;
$response_message = ($is_valid)
		? 'Success'
		: implode(PHP_EOL, $ObjectValidator->getAllMessages());

...

// The resulting object property values:
$FormObject->name                 => 'Nonya'
$FormObject->is_public_review     => false
$FormObject->star_rating          => 4
$FormObject->review_text          => 'I liked this product.'
$FormObject->review_image_uploads => [ [ `File 1 Data` ], [ `File 2 Data` ] ]
```

<p align="right">(<a href="#readme-top">back to top</a>)</p>

## Documentation
This library is fairly simple, it contains a base Struct class with two traits that incorporates hydration and validation. It also contains some pre-made attributes for you to use that take care of some of the more common things. I've also built a demo for you to use and play with. How to run the demo is at the bottom of this readme.

#### Useful Info:
* Attributes are run in order from top to bottom.
* Only the attributes associated with the method that was called are run.
* Each helper only looks at an object's public properties.
	* Yes, I can technically also do the private/protected properties using reflection. It won't happen because that breaks the whole purpose behind private/protected. That said, if there is a use case that would be deemed essential to have that feature, I can look into opening that up. It better be a good reason though.
* For attribute arguments, use named parameters. It makes things easier for everyone. You can do it the old way if you want, but I recommend using named parameters where you can for self documenting code.
* When using this library to handle form submissions, it is highly recommended to have default values for any property that has form data that may not be sent over. Like checkboxes. Otherwise PHP might start yelling at you about accessing uninitialized properties.
* Hydrating properties which can be converted from a string can be hydrated with an object as long as the `__toString()` magic method is set up.
* Fun Fact: I use the demo as a testing ground for changes.
* Cool Fact: This library works with readonly properties to make the object immutable. So you can pass it around without worrying if the data will change along the way.

Now, on to the actual docs...

<p align="right">(<a href="#readme-top">back to top</a>)</p>

### The Hydrator
This method takes an assoc array of data and hydrates the object's public properties while also converting the data to the proper types (when it can). The property name must match an array key in the incoming array.

Hydration attributes are pre-hooks into the assigning of the properties. They use the incoming value to transform it into something different before assigning it. They can also be used as a way to clean the data, and run pre-validation. As a bonus, fully chainable.

Because hydration processes the value before assigning it to the property, you can make properties `readonly` to make the data immutable.

#### Basic Usage:
```PHP
// via the constructor:
$SomeStructObj = new SomeStructObj($_POST);

// via hydrate method:
$SomeStructObj->hydrate($_POST);
```

#### Conversions
When hydrating an object, the data that is passed in is not always the type you need. So I will try and convert it for you behind the scenes. You can disable my enhanced conversion if you want to use PHP's type coercion instead.

Conversion happens happens in one of 2 places:
1. When enhanced conversion is enabled, it will convert after all of the hydration attributes have been processed, and right before assigning it to the property.
1. When enhanced conversion is disabled, PHP's built in type coercion kicks in while assigning it to the property.

Important Note: Due to how properties are assigned values, it is impossible to turn off conversion completely. Blame PHP for how it handles `strict_types=1` in its internal classes.

**Enhanced Conversion Notes:**
* Bools use PHP's `filter_var` to convert common bool strings. When used with a default value, checkbox values in forms become very simple to manage.
* Array conversions are only done on empty values. Everything else will fail. If you want to convert a value to an array, please create a hydration attribute.
* For simplicity, conversions are skipped if the data type of the property is some sort of object. That opens too many cans of worms to deal with. You'll need to make your own custom Hydration attribute if you want to populate object properties.

#### Attributes
These hook into the assignment process and will run pre-processing on the incoming value before assigning it. This is so you can take one value and assign a different one. This has some extreme potential because of that. For example, you can easily set up a custom attribute to take an ID, run a query or use a mapper, populate a different object, then assign that to the property instead of that simple ID.

Hydration attributes can also be used as a way to pre-validate the incoming data, or lack thereof, like the `Required` attribute.

**Hydration Settings Attribute**
This is a special attribute that can be applied to the whole class, or individual attributes. It tells the hydrator what to do and what not to do. By default, hydration and conversion will always run if it can.
* `#[HydratorSettings()]` - This is the settings attribute that is used to enable or disable certain aspects of the hydrator for the property.
	* **Optional Parameter:** `hydrate: bool` [default: true] - This enables/disables hydration completely for the property (or class if put on the class). Type conversions will not run if this is disabled for obvious reasons.
	* **Optional Parameter:** `enhanced_conversion: bool` [default: true] - This enables/disables enhanced type conversions. If set to false, it will use PHP's type type coercion to convert the values.
		* The enhanced conversion is more flexible with the incoming values. For example, it converts boolean strings like 'on'/'off', objects with `__toString()` can be converted to string, and array typed properties will convert empty values to an empty array.

**Hydration Attributes**
* `#[FileUpload]` - Specify if a property was an upload(s) and automatically pull the data from $_FILES.
	* **Optional Parameter:** `transpose: bool` [default: true] - This will format PHP's awkwardly organized multi-uploads array into an array for each uploaded file as an element with the format of a single upload.\
	<code>[ [file1 data], [file2 data] ]</code>
	* **Property Data Type Restriction:** Array compatible fields only.
	* **Special Note:** This will remain an array exclusive because everyone has a different file data class which are all initialized differently. If you want to use your own file data class, ignore this attribute and make a custom hydration attribute which has the logic to set up your desired object.
* `#[JsonDecode]` - Exactly what PHP's `json_decode` does. Takes a JSON string and tries to convert it to an array. The optional constructor arguments match PHP's method as well.
	* **Optional Parameter:** `associative: bool|null` [default: null] - Determines if the value should be parsed as an associative array or not.
	* **Optional Parameter:** `depth: int` [default: 512] - Specified recursion depth.
	* **Optional Parameter:** `flags: int` [default: 0] - Bit mask of JSON decode options.
	* **Property Data Type Restriction:** Array compatible fields only.
* `#[Required]` - This will throw a `HydrationException` if the property doesn't have a matching key in the incoming array. Basically it is just an `array_has_key` check.
* `#[TypedArray]` - Convert all values in the incoming array to a specific scalar type [ bool, int, float, string ].
	* **Parameter:** `type: string` - This is the type you wish to convert the array values to.
	* **Property Data Type Restriction:** Array compatible fields only.
* `#[HtmlSpecialChars(flags: int, encoding: string|null, double_encode: bool)]` - This behaves exactly like PHP's `htmlspecialchars` and takes the same parameters.
	* **Property Data Type Restriction:** String only.
* `#[StrReplace(search: string|array, replace: string|array)]` - This behaves exactly like PHP's `str_replace`.
	* **Property Data Type Restriction:** String and Array only. Those are the types that work in PHP's `str_replace`.
* `#[Trim]` - That's what we all want this library for, now you got it. With the Trim attribute, any data in that property is trimmed.
	* **Property Data Type Restriction:** String only.
	* **Optional Parameter:** `characters: string` - The characters are the same param that is passed to PHP's `trim` function.
* `#[UseDefaultOnEmpty]` - When the current value passes an `empty` check, reflection looks at the property's default value, and then uses that instead.
	* Pro Tip: Combine with `#[Trim]` to clean up blank form fields with a single space in them.
 	* Do not use on readonly properties since they cannot have defaults. Use `UseValueOnEmpty` instead.
* `#[UseValueOnEmpty(value: mixed)]` - When the current value passes an `empty` check, it will use the provided value instead.
	* Pro Tip: Similar with `UseDefaultOnEmpty`, combine with `#[Trim]` to clean up blank form fields with accidental spaces.

#### Hydration Attribute Properties
These are set when a Hydration Attribute class is initialized. They can be used in your own attributes if your attribute extends the `AbstractHydratorAttribute` class.

* `public ReflectionProperty $Property;` - The ReflectionProperty object to look up information about the property.
* `public bool $value_exists = false;` - This is true if a key matching the property's name is in the incoming array.

<p align="right">(<a href="#readme-top">back to top</a>)</p>

### The Validator
This trait contains a method that validates the data in an object's public properties and returns `ValidationResponse`, which will be described below. The validation runs on each parameter that has one or more validation attributes.

There is an optional, but recommended, attribute for you to use to customize the validation failure messages for any validation attributes on a property. This is explained below in the **Custom Failure Messages** section.

#### Basic Usage:
```PHP
// Object has to be a struct
$Obj = new SomeStructObj($_POST);

// Run validation attributes which returns a validation response object
$ValidationResponse = $Obj->validate();

if (!$ValidationResponse->success)
{
	$failure_messages = $ValidationResponse->getAllMessages();
}

print_r($failure_messages);
```

#### Validator Attributes
* `#[NotEmpty]` - The value must pass an `!empty` check.
	* Pro Tip: Combine with `#[Trim]` for validating form fields.
* `#[RegexMatch(pattern: string)]` - Whether or not you can remember how to write a regex is a different issue.

#### Validator Attribute Properties
These are set when the specific Attribute class is initialized. They can be used in your own attributes if your attribute extends the `AbstractValidatorAttribute` class.

* `public ReflectionProperty $Property;` - The ReflectionProperty object to look up information about the property.
* `public bool $is_initialized = false;` - Tells you if the property has been initialized with a value or not.

#### Custom Failure Messages
This is not so much a validation attribute as it is a validation helper attribute. This is so that error messages coming from the validation will be more useful for everyone. Here is an example of how it is used on a property.

```PHP
#[Trim]
#[NotEmpty]
#[ValidatorFailureMessage(NotEmpty::class, 'The email field is required!')]
public string $email;
```

When the validation method is run, and the value in $email is empty, that error will be added to the messages array and the object will ultimately fail the validation check.

Side note: As you can see, I'm not using named properties for this since it is fairly simple. First param is the validation attribute the message is for, the second param is the failure message. The attribute declaration for these when using named params can get lengthy, especially for longer messages, so I omitted them in all my examples and demo. Feel free to use named params though, it won't break anything to use them, except maybe your linter.

#### ValidationResponse
This is the object that is returned to you after the validator has run. It contains an organized way to track all the validation failure information. This object can be used in `json_encode` for easy breakdown if needed.

**ValidationResponse Methods**
* `public function getAllMessages(): array` - This is a quick way to grab all the failure messages in a single one dimensional array of strings, instead of having to go through each property yourself. 

**ValidationResponse Public Properties**
* `public readonly bool $success;` - This is the value that determines if the Struct's validation was successful.
* `public readonly array $property_responses;` - This contains a map of all the property names that failed validation, and their `PropertyValidationResponse` object which holds the failure information of that property (described below).

#### PropertyValidationResponse
This is the response returned if a property failed validation. It contains some debug data as well as an array of failure messages for the property. This object can be used in `json_encode` for easy breakdown if needed. It also chains with `ValidationResponse` so calling `json_encode` on `ValidationResponse` will also break these objects down automatically.

**PropertyValidationResponse Public Properties**
* `public readonly mixed $value;` - This is some debug data. It is the value that caused the validations to fail for this property.
* `public readonly array $messages;` - Just an array of strings that contains all the failure messages for this property.

<p align="right">(<a href="#readme-top">back to top</a>)</p>

## How to run the demo
Make a copy of this project via cli or by downloading. Run `composer install` in the project root, then run the following command (also in the project root) to spin up a dev PHP server for the demo:
```shell
php -S localhost:8000 demo/index.php
```

Then use a web browser on the same computer to visit the following url:\
http://localhost:8000/

<p align="right">(<a href="#readme-top">back to top</a>)</p>

## Project Coding Standards
* 100% Code Coverage via integration tests
* PHP Stan Level 9

<p align="right">(<a href="#readme-top">back to top</a>)</p>

<!-- 
## Contributing
I gladly welcome feedback and suggestions via Github Issues. I thrive on constructive feedback.

Bugs of course are submitted via Github Issues as well.

#### Code Styling Basics
* Curly braces `{ }` start on new lines. It is cleaner to look at.
* Classes are PascalCase.
* Methods are camelCase.
* Variables that hold objects use PascalCase, otherwise they use snake_case.
* Use strict typing as much as possible.
* Run `composer beautify` before staging commits.

#### Testing Requirements
* PHP Stan Level 9.
* All tests should be integration tests. Aka, should run like you are actually using the library.
* 100% Coverage is a hard requirement. Combined with the previous one, we can find unreachable code and remove it.
* Method mocking is a last resort.
* You need a really good reason to use `@codeCoverageIgnore` or similar flags.

<p align="right">(<a href="#readme-top">back to top</a>)</p>

-->

_<h5>tab indentation is better. #teamtabs</h5>_

<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->
[stars-shield]: https://img.shields.io/github/stars/todo-make-username/data-processing-struct.svg
[stars-url]: https://github.com/todo-make-username/data-processing-struct/stargazers
[issues-shield]: https://img.shields.io/github/issues/todo-make-username/data-processing-struct.svg
[issues-url]: https://github.com/todo-make-username/data-processing-struct/issues
[license-shield]: https://img.shields.io/github/license/todo-make-username/data-processing-struct.svg
[license-url]: https://github.com/todo-make-username/data-processing-struct/blob/main/LICENSE
[php-shield]: https://img.shields.io/badge/php->%3D8.2-blue
[php-url]: https://www.php.net/
