export default {
    methods: {
        showErrorsMessages(validator,errorResponse){
            let errorFields = Object.keys(errorResponse);
            let errorsHtml = '<ul style="list-style: none">';
            errorFields.map(field => {
                let errorString = errorResponse[field][0];
                errorsHtml += '<li>' + errorResponse[field][0] + '</li>';
            });
            errorsHtml += '</ul>';
            this.$swal({position: 'top', type: 'error', html: errorsHtml,
                showConfirmButton: false, timer: 113500, toast: true});
        },
        getTrans(string){
            return this.$options.filters.trans(string);
        },
        getTransAttr(string,attr){
            return this.$options.filters.trans(string,attr);
        },
        swalConfirm: function (callback) {
            let self = this;
            self.$swal({
                title: 'Are you sure?',
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: 'Yes',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.value) {
                    callback()
                }
            });
        }
    }
};
