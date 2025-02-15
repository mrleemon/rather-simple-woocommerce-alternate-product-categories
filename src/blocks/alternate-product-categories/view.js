import { store, getContext, getElement } from '@wordpress/interactivity';

store('rswapc-store', {
	actions: {
		redirect(event) {
			const context = getContext();
			const home_url = context.homeURL;
			var this_page = '';
			if (home_url.indexOf('?') > 0) {
				this_page = home_url + '&product_cat=' + event.target.value;
			} else {
				this_page = home_url + '?product_cat=' + event.target.value;
			}
			location.href = this_page;
		},
	}
});
