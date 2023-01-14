import AppForm from '../app-components/Form/AppForm';

Vue.component('translatable-form', {
    mixins: [AppForm],
    data: function() {
        return {
            form: {
                title:  '' ,
                perex:  '' ,
                published_at:  '' ,
                
            }
        }
    }

});