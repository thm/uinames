uinames.com
=======

This is the repo for [uinames.com](http://uinames.com). Feel free to contribute to the project by adding names. Keep in mind that more isn't better. Quality over quantity. Quality being the most common/popular names in a country.

### The Algorithm
When the option to pick a country at random is selected, a country will be picked based on the amount of possible name-combinations for that country. A country with more names is more likely to be picked, and countries with less names are less likely to be picked. I propose having a maximum of 100 male names, 100 female names and 300 last names per country. That's 60.000 possible combinations per country.

### The Layout (JSON)
    [
      [
        {"country":"Example"},
        ["Male","Names"],
        ["Female","Names"],
        ["Last","Names"]
      ],
      [etc]
    ]

### The API
All responses are returned as JSON, and there is no request limit. Please keep the amount of calls to a minimum though, and cache responses whenever possible.

#### Basic usage
	http://api.uinames.com
	---
	{
	  "name":"John",
	  "surname":"Doe",
	  "gender":"male",
	  "country":"United States"
	}
#### Optional Parameters
The amount of names to return:
<pre>http://api.uinames.com/<strong>?amount=25</strong></pre>

The gender of names to return (male or female):
<pre>http://api.uinames.com/<strong>?gender=female</strong></pre>

Country specific results:
<pre>http://api.uinames.com/<strong>?country=germany</strong></pre>

### Author
This project was initiated and is being maintained by [@thomweerd](http://twitter.com/thomweerd).

### Credit
This massive collection of names wouldn't have been as complete without the help of [these wonderful people](https://github.com/thm/uinames/network/members). Thanks for all the contributions and the continued support!

Special thanks go out to [Claudio Albertin](http://github.com/ClaudioAlbertin) for his work on the API.