import './bootstrap';
import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import solarHero from './hero';

Alpine.plugin(collapse);
Alpine.data('solarHero', solarHero);
window.Alpine = Alpine;
Alpine.start();
