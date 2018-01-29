# Eurest bestelsysteem

## Documentatie

- [Database migrations en seeding](http://phinx.org)
- [Database queries maken](http://clancats.io/hydrahon/developer)
- [Twig templates](https://twig.symfony.com)

### Prijs berekening

Item (product):

	- price: item price
	- salePrice: item sale price
	- effectivePrice: sale price if sale, else normal
	- vat: price * 0.06
	- saleVat: salePrice * 0.06
	- effectiveVat: effectivePrice * 0.06

Order Item:

	- price: price of a single item
	- salePrice: price of single item if sale
	- effectivePrice: price of single item, if sale sale_price else normal price
	- vat: price of a single item * 0.06
	- saleVat: price of a single item if sale * 0.06
	- effectiveVat: the sale vat if sale, else normal vat
	- totalPrice: price of single item * amount of items
	- totalSalePrice: sale_price of single item * amount of items
	- effectiveTotalPrice: sale_price if in sale, else normal price * number of items
	- discount: The original price - sale_price if in sale
	- totalDiscount: the original price - sale price * amount

Order:

	- totalPrice: price of all orderItems
	- effectiveTotalPrice: price of all orderItems, taking into account sale prices
	- subtotalPrice: price of all orderItems - vat
	- effectiveSubtotalPrice: effective price - effective sale price
	- totalVat: totalPrice * 0.06
	- effectiveTotalVat: totalEffectivePrice * 0.06

#### Helper methods

Product en orderItem hebben een `isSale()` methode
Om een prijs te tonen in twig gebruik je de `money` filter. Voorbeeld: {{ order.effectiveTotalPrice|money }}.
Check bijvoorbeeld `views/frontend/account/orders/view.twig`

#### Logins Clow

###### Email login:

	eurestdemo@gmail.com
	Eurest123

###### Eurest login:

	- admin:
		eurestdemo+admin@gmail.com
		password

	- employee:
		eurestdemo+employee@gmail.com
		password

	- teacher:
		eurestdemo+teacher@gmail.com
		password

	- student:
		eurestdemo+student@gmail.com
		password