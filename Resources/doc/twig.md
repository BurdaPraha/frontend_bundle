# Twig features

## Filters

- `{{ asset('/path/file.css')|version }}` fill version parameter by the last change timestamp
- `{{ currency_iso|currencyToSymbol }}` from ISO to symbol like: €, Kč
- `{{ some_your_price|goPayToHuman }}` number divided 100 (gopay.cz requirement)

## Functions

- `{{ detectOS() }}` get android or ios string for special styles