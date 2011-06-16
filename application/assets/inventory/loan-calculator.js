var dealertrend = jQuery.noConflict();

dealertrend( document ).ready( function() {

	if( dealertrend( '#loan-calculator' ).length ) {
		calculateLoan();

		dealertrend( '#loan-calculator input' ).change( function() {
			calculateLoan();
		}

		dealertrend( '#loan-calculator button' ).click( function() {
			calculateLoan();

			return false;
		}
	}

}

function iCanHazMoney( amount ) {
	value = amount.toString().replace( /\$|\,/g ,'' );

	if( isNaN( amount ) ) value = "0";

	sign = ( value == ( value = Math.abs( value ) ) );
	value = Math.floor( value * 100 + 0.50000000001 );
	cents = value % 100;
	value = Math.floor( value / 100 ).toString();

	if( cents < 10 ) cents = "0" + cents;

	for( var i = 0; i < Math.floor( ( value.length - ( 1 + i ) ) / 3 ); i++ )
	 value = value.substring( 0 , value.length - ( 4 * i + 3 ) ) + ',' + value.substring( value.length - ( 4* i + 3 ) );

	return ( ( ( sign ) ? '' : '-' ) + '$' + value + '.' + cents );
}

function calculateLoan() {
	// Get the HTML
	var price_html = dealertrend( '#loan-calculator-price' ).val();
	var interest_rate_html = dealertrend( '#loan-calculator-interest-rate' ).val();
	var term_html = dealertrend( '#loan-calculator-term' ).val();
	var trade_in_html = dealertrend( '#loan-calculator-trade-in-value' ).val();
	var down_payment_html = dealertrend( '#loan-calculator-down-payment' ).val();
	var sales_tax_html = dealertrend( '#loan-calculator-sales-tax' ).val();

	// Convert it into numbers.
	var price = Number( price_html.replace( /[^0-9\.]+/g , "" ) );
	var interest_rate = Number( interest_rate_html.replace( /[^0-9\.]+/g , "" ) );
	var term = Number( term_html.replace( /[^0-9\.]+/g , "" ) );
	var trade_in = Number( trade_in_html.replace( /[^0-9\.]+/g , "" ) );
	var down_payment = Number( down_payment_html.replace( /[^0-9\.]+/g , "" ) );
	var sales_tax = Number( sales_tax_html.replace( /[^0-9\.]+/g , "" ) );

	// Do math.
	var total_price = price;
	total_price -= down_payment;
	total_price -= trade_in;
	total_price += ( total_price * ( sales_tax / 100 ) );

	var total_cost = total_price;
	interest_rate /= 1200
	var monthly_payment = interest_rate * total_price / ( 1 - Math.pow( 1 + interest_rate , -term ) );
	total_cost = monthly_payment * term + down_payment + trade_in;

	// ...?
	dealertrend( '#loan-calculator-bi-monthly-cost' ).html( iCanHazMoney( monthly_payment / 2 ) );
	dealertrend( '#loan-calculator-monthly-cost' ).html( iCanHazMoney( monthly_payment ) );
	dealertrend( '#loan-calculator-total-cost' ).html( iCanHazMoney( total_cost ) );

	// PROFIT!
}
