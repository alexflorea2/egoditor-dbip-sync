import Vue from 'vue';
import VueI18n from 'vue-i18n';

Vue.use(VueI18n);

const messages = {
    'en': {
        'informations': 'informations'
    },
    'es': {
        'informations': 'informaciones',
        'primary contact': 'contacto primario',
        'usage':'uso',
        'upgrade':'potenciar',
        'Scans':'escaneos',
        'first name':'nombre de pila',
        'last name':'apellido',
        'phone number':'número de teléfono',
        'company':'empresa',
        'street':'calle',
        'city':'ciudad',
        'zip':'Código Postal',
        'country':'país',
        'website':'sitio web',
        'password':'contraseña',
        'login email':'ingreso (correo)',
        'please provide your contact information bellow':'proporcione su información de contacto a continuación',
        'please check form': 'por favor revise el formulario'
    }
};

const i18n = new VueI18n({
    locale: 'en', // set locale
    messages, // set locale messages
});

export default i18n;
