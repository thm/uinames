uinames.com
=======

This is the repo for [uinames.com](http://uinames.com). Feel free to contribute to the project by adding names. Keep in mind that more isn't better. Quality over quantity. Quality being the most common/popular names in a country.

### The Algorithm
When the option to pick a country at random is selected, a country will be picked based on the amount of possible name-combinations for that country. A country with more names is more likely to be picked, and countries with less names are less likely to be picked. I propose having a maximum of 100 male names, 100 female names and 300 last names per country. That's 60.000 possible combinations per country.

### The Layout (JSON)
    [
      {
        "region": "Country",
        "male": ["Male", "First", "Names"],
        "female": ["Female", "First", "Names"],
        "surnames": ["Last", "Names"]
      },
      {etc}
    ]

### The API
All responses are returned as JSON(P), with no request limit. Please keep the amount of calls to a minimum though, and cache responses whenever possible.

#### Basic usage
	http://uinames.com/api/
	---
	{
	  "name":"John",
	  "surname":"Doe",
	  "gender":"male",
	  "country":"United States"
	}
#### Optional Parameters
Amount of names to return, between `1` and `500`:
<pre>http://uinames.com/api/<strong>?amount=25</strong></pre>

Limit results to the `male` or `female` gender:
<pre>http://uinames.com/api/<strong>?gender=female</strong></pre>

Region-specific results:
<pre>http://uinames.com/api/<strong>?country=germany</strong></pre>

Require a minimum number of characters in a name:
<pre>http://uinames.com/api/<strong>?minlen=25</strong></pre>

Require a maximum number of characters in a name:
<pre>http://uinames.com/api/<strong>?maxlen=75</strong></pre>

For JSONP, specify a callback function to wrap results in:
<pre>http://uinames.com/api/<strong>?callback=example</strong></pre>

#### Exception handling
Error messages have the following format:
<pre>{"error":"Region or language not found"}</pre>

### Author
This project is initiated and maintained by [@thomweerd](http://twitter.com/thomweerd).

### Credit
This massive collection of names wouldn't have been as complete without the help of [these wonderful people](https://github.com/thm/uinames/network/members). Thanks for all the contributions and the continued support!

Special thanks to [Claudio Albertin](http://github.com/ClaudioAlbertin) for his work on the API.
