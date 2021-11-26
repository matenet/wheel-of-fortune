# wheel-of-fortune
Interfaceless game Wheel of fortune written in PHP

Use 'composer install' to install app.
Use 'php vendor/bin/phpunit' to run tests.

--------------------------------------------------

Polish description:

Napisano obiektową implementację uproszczonej wersji popularnej gry "Koło fortuny". Gra polega na próbie odgadnięcia zakrytego słowa poprzez próby zgadywania poszczególnych liter.

Zasady gry:
- gracz wybiera literę; jeśli litera ta znajduje się w słowie wszystkie pozycje tej litery zostają "odsłonięte"; jeśli litery nie ma w słowie, zapisywana jest jako błąd;
- gracz ma możliwość popełnienia 6 błędów (sześć razy trafić literę której nie ma w słowie; 7-te błędne trafienie spowoduje zakończenie gry)
- gracz wygrywa jeśli wszystkie litery ze słowa zostaną odgadnięte
- gracz ma możliwość odgadnięcia całego słowa od razu, które kończy grę; jeśli słowo jest poprawne - gracz wygrywa
- każdą literę można sprobówać tylko raz
- wielkość liter nie ma znaczenia
- brane są pod uwagę tylko litery alfabetu łacińskiego

Do uruchomienia gry potrzebujesz jedynie instancji PHP w wersji wyższej bądź równej 7.2 oraz Composera (​ https://getcomposer.org/​ )

POWODZENIA!
