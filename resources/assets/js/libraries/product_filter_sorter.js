class ProductFilterSorter {

    constructor () {
        this.getters = [
            'productFilterStatus',
            'sorterQueryString',
            'productFilterBrands',
            'productFilterCategories',
            'productFilterSizes',
            'productFilterColors',
            'productFilterCapacities',
            'productFilterPackages',
            'productFilterLang'
        ]
    }

    onChange (state, getters, onlyFilter) {
        var url = state.appModule.product_list_url
        var queryStrings = []

        //Price filter
        if (onlyFilter) {
            var query = getters[onlyFilter]
            console.log(query)
            if (query) {
                queryStrings = queryStrings.concat(query)
            }
        } else {
            for (var i = 0; i < this.getters.length; ++i) {
                var query = getters[this.getters[i]]
                if (query) {
                    queryStrings = queryStrings.concat(query)
                }

            }
        }


        var urls = []
        for (var i = 0; i < queryStrings.length; ++i) {
            var strs = []
            for (var k in queryStrings[i]) {
                strs.push(k)
                strs.push(queryStrings[i][k])
            }
            urls.push(strs.join('='))
        }

        if (urls.length > 0) {
            urls.push('r=' + new Date().getTime())
        }

        urls = urls.join('&')

        if (url.match(/\?/i)) {
            url = url + '&' + urls
        } else {
            url = url + '?' + urls
        }

        window.location = url + '#product-list'

    }

}

export default ProductFilterSorter