import base from './tailwind.config.js';

/**
 * Tailwind config for the public Blade bundle (resources/css/public.css,
 * loaded by blade-app.js): same theme as the main config, but only scans
 * the public templates/scripts, so the CSS the visitors download doesn't
 * carry every utility the admin panel uses.
 */
export default {
    ...base,
    content: [
        './resources/views/components/**/*.blade.php',
        './resources/views/partials/**/*.blade.php',
        './resources/views/pages/**/*.blade.php',
        './resources/js/blade-app.js',
        './resources/js/space/**/*.js',
        './resources/js/Components/Public/**/*.vue',
    ],
};
