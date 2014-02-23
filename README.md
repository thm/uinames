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

Just a little project by [@thomweerd](http://twitter.com/thomweerd).
