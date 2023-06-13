let mix = require("laravel-mix");

mix
  .sass("resources/css/app.scss", "public/css")
  .js("resources/js/app.js", "public/js/app.js")
  .js("resources/js/manon.js", "public/js/manon.min.js");
