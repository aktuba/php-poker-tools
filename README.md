# php-poker-tools

A lightweight command line tool for calculating poker hand probabilities.

### Usage

```bash
php ./bin/poker.php odds AcKh KdQs   # any number of hands supported

#support commands
odds # hold'em odds

# options
-b, --board Td7s8d     # community cards
-i, --iterations=1000  # number of preflop simulations to run, default: 100000
-e, --exhaustive       # run all preflop simulations
-p, --possibilities    # show individual hand possibilities
-h, --help             # show help
```

### Examples

Use `--board` or `-b` to define community cards.

![--board example](https://user-images.githubusercontent.com/423239/141687057-b3cdd41d-63d7-44f6-bbc6-d4f3ce25ac18.png)

Use `--exhaustive` or `-e` to run all preflop simulations. Note that this will take some time.

![--exhaustive example](https://user-images.githubusercontent.com/423239/141687126-d1a45946-f16b-4fe7-b2ee-414d0ead1ab5.png)

Use `--possibilities` or `-p` to show all possible hand outcomes. Hand possibilities are shown by default if only one hand is defined.

![--possibilities example](https://user-images.githubusercontent.com/423239/141687244-fc8df954-e17e-4242-a4a0-31452b17c36e.png)

Use `--iterations` or `-i` to show simulation results for a certain number of randomly generated boards.

![--possibilities example](https://user-images.githubusercontent.com/423239/141687487-67e3c717-afd8-45b7-a522-8a0be256afbc.png)

Created based on https://github.com/cookpete/poker-odds
