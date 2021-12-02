import DetailField  from './components/DetailField';
import FormField from './components/FormField';

Nova.booting((Vue, router, store) => {
  Vue.component('detail-laraberg-nova', DetailField)
  Vue.component('form-laraberg-nova', FormField)
})
