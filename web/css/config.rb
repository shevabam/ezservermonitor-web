environment = :production

output_style = (environment == :production) ? :compressed : :expanded

http_path = "/"
css_dir = "."
sass_dir = "_src"
images_dir = "../images"
fonts_dir = "./fonts"
javascripts_dir = "../js"
relative_assets = true
line_comments = false
asset_cache_buster :none