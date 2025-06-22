import axios from 'axios';
window.axios = axios;
import Konva from 'konva'

window.Konva = Konva

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
