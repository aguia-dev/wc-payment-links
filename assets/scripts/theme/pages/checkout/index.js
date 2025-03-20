document.addEventListener('DOMContentLoaded', async function () {
	const token = document.querySelector('#wc-payment-links-checkout-token');
	console.log(token)
	if (!token) return;

	const response = await fetch('/wp-json/wc-payment-link/v1/fill-cart/' + token.value, {
		method: 'POST'
	});

	if (!response.ok) {
		console.error("Erro ao preencher carrinho");
	}
});
