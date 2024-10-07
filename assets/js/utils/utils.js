$(document).ready(function() {

    //tf - table footer
    Utils = {
        itemId: null,

       getTableFooterParent: (selector) => {
            return $(selector);
        },

        getItem: () => {
            return $(`#${Utils.itemId}.stock-list`);
        },

        getTableFooterCount: () => {
            $tfCountParent = Utils.getTableFooterParent('.tf-res-stock-count');

            let tfCount = null;
            
            if($tfCountParent.length) {
                tfCount = $tfCountParent.html();
                tfCount = input_validate_price(tfCount);
                tfCount = parseInt(tfCount);
            }

            return tfCount;
        },

        getItemOldCount: () => {
            let old_count = Utils.getItem().find('.res-stock-count').find('.stock-list-title').html();
    
            return old_count;
        },



        getTableFooterPrice: () => {
            $tfPriceParent = Utils.getTableFooterParent('.tf-res-stock-sum');

            let tfPrice = null;

            if($tfPriceParent.length) {
                tfPrice = $tfPriceParent.html();
                tfPrice = input_validate_price(tfPrice);
                tfPrice = parseInt(tfPrice);
            }
            
            return tfPrice;            
        },

        getItemOldPrice: () => {
            let old_price = Utils.getItem().find('.res-stock-first-price').find('.stock-list-title').html();
            
            return old_price;
        },

        getFooterData: (selector) => {
            let $ss = $(selector);
            let tf = 0;

            if($ss.length) {
                tf = $ss.html();
                tf = input_validate_price(tf);
                tf = parseInt(tf);
            }            
            
            console.log(tf);
        }

    }

});