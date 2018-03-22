# Prosongsa

## Setup

* Have the ability to serve php files.
* Clone this repository, including the submodules, into such a place.

	```git clone --recursive https://github.com/earboxer/prosongsa.git```

	If you've already cloned without the submodules, you can use
	`git submodule update --init --recursive`.

* Create file `inputfile.txt` in this directory
	(see `inputfile.example.txt` for example format).
* Navigate to the page where index.php is being served from.

* `cd chordsdata && ./commands.sh` to create the data files

To update this repository

```sh
git pull && git submodule update --init --recursive && git submodule update &&
 cd chordsdata && ./commands.sh
```

## Acknowledgements

* Github user [mzarillo](https://github.com/mzarillo) for
	[ccharter](https://github.com/earboxer/ccharter) script.
* [Ruslan Keba](https://github.com/rukeba) for
	[guitar chords fingering data](http://guitar-chords-chart.net).
