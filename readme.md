# Developer readme

## Install

### Requirements
- PHP 8.1.0 or higher
- WP Core 6.6 or higher

### Instructions
Navigate to the `/wp-content/plugins` directory
- `git clone https://github.com/MaxwellGarceau/songwriter-tools.git`
- `composer install`
- `npm install`
- `npm start` - Runs `wp-scripts start` - compiles TS and Scss

Below are a list of some of the technical key points of this plugin.

### Backend

- **PHP** in the context of a WordPress plugin
  - WP REST API
  - Nonce
  - Sanitization
  - Validation
  - Auth
  - **Controller** - uses the Command design pattern
  - **Song CPT** programmatic creation on successful audio upload
    - Has the audio attachment ID associated
  - **Autoloading** with Composer and PHP DI
  - **Object Oriented Programming** paradigm complemented by design patterns
  - PHP 8.3 features: typehinting, constructor promotion, enums, etc.
  - **Logging** with Monolog configured to integrate with the `WP_DEBUG` constants

### Frontend

- **A custom Gutenberg block** and JavaScript in the context of WordPress
  - **Song Upload block**
  - **Edit settings**
    - Alignment, text alignment, text color, background color
    - Heading content, size, and tag
    - Max file size, allowed file types
  - **Can upload audio**
  - **WP Interactivity API** on `render.php` and `view.ts`
    - Combines front-end event listeners with state management for enhanced interaction
    - Uses `wp-on-submit`, `wp-on-change`, and `wp-bind` to handle file selection, submission, and front-end state control
  - **Error handling**
    - The front end of the block updates as songs are selected and submitted
  - **Global styles** - the Song Upload block accepts base WP styles
    - Combination of inheriting `theme.json` styles, setting neutral `block.json` styles
    - Low specificity styling, fallbacks to global WP CSS variables, and HTML elements with WP element class names
  - **Cross-theme compatibility styling**
    - Inherits from WP-supported styles where possible and sets neutral fallbacks where themes do not support them
  - **Responsive design** principles and techniques without using media queries
  - TypeScript used exclusively
  - `wp-scripts` as a build tool
  - Modern JS/TS including ES6+/ES2023
  - **Modular imports and exports** via Webpack (used under the hood)
  - Assets compiled down to a single, optimized file
  - **SCSS**
    - BEM methodology
    - Object Oriented CSS (very lightly to separate form structure from style)
    - Use of mixins to make code more flexible
    - Low specificity styles
    - Minimal CSS written for `editor.scss`

### Roadmap

I’d like to expand the Song CPT functionality and integrate it with audio playlists and post feeds. Additionally, I’d like to explore the possibility of supporting an integration that allows users to upload recordings from their phone since that’s where many songwriters keep their ideas.

As I’ve mentioned elsewhere, the **Song Upload block** is part of a series of tools I’m developing for a **music community WordPress theme** that will allow songwriting groups to collaborate with each other online. Other tools I’d like to develop include:

- **Gutenberg front-end editable lyric blocks** - essentially, comments editable by other users that contain version history and special styling to enable lyric ideas and suggestions
- **A plagiarism detector** (my other submission!) that allows users to reference their lyrical ideas with Genius.com
- **An audio player** that accepts songs as an input source
