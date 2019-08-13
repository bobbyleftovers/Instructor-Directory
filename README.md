# WIP: Instructor/Studio Directory
Tis is my current project. I can't share the actual repo this is associated with but I am leaving this here for a time because I think it represents me well in terms of WordPress knowledge. The request in front of me was to make a scalable solution for an instructo/studio finder for a yoga guru who taught other instructors. originally there was a specific plugin in mind for this idea but my argument in favor of a custom build was that the pre-built plugin A) did not have geo-location and B) was built with a very different purpose in mind. The client agreed.

This plugin will create a simple but easily extandable directory of all certified instructors and studios that those instructors work in, all of which can be filtered using geolocation. It will also allow instrucotrs to register and add their own profiles to the searchable posts. A user role is added for instructors and they are not able to get into the actual admin area, rather a small admin area is created for them. It requires the use of Advanced Custom Fields and a [geolocation field extension](https://github.com/bobbyleftovers/acf-mapbox-field) for ACF that I created. It is namespaced to prevent crashing into other plugins and uses VueJS for the frontend.

## Dependencies:
- Advanced Custom Fields 5+
- Mapbox field type extension for ACF

## How it works
- On install + activation, this plugin will create a table to store most of the ACF data. You will also see two new post types for 'Instructor' and 'Studio' as well as new taxonomies for 'language' and 'certifications'. The post types and taxonomies were particular to a specific project but you can rename them as needed. You can also specify table column names as needed.

- On activation we also create several pages (If the page already exists, it will not be re-rcreated):
  - Directory: This is the main directory page users will come to when searching. It runs on VueJS and the WP-REST API.
  - Registration: This is where instructors go to create their account. On registration success they are given a user account and post of the type 'instructor' is created. the post is what non-logged in users see on the directory page. An association between the two is added to the users meta to maintain the profile-to-post relationship. The new instrucors post is initialized in draft mode to prevent displaying incomplete profiles.
  - Login: There is a custom backend made just for users with the 'instructor role'. This page and the classes behind it are responsible for logging a user in and/or redirecting.
  - My Account: This will show all of the users profile data as customers would see it. There are two links to edit either location or other profile fields.
  - Profile Editor: Here, the instructor can edit their name, email, social media links and more. Some fields require certian other updates. Updating the email will update that email in both the post and the profile to keep them in sync, while updating first/last name will change the users name as well as update the post title and slug to keep all aspects matched up.
  - Location Editor: This uses the ACF Mabox field extension and allows the instructor to type in as-exact an address as the want. Clicking/dragging on the map will move the pin to a place the user chooses. Only the city and state will appear on the front end, so those are required but not street/postal code. In location searches we use the lat/long values generated here to determine an instructors distance from a given set of coordinates.
  - Password Reset Request/ Password Reset Form: These two pages work to accept user input for the 'lost password' workflow.

- There are also templates created for single instructors and studios. Studio posts are added by a site admin.

### Features
- Custom Database Table: This is created to prevent the bloating of the wp_postmeta table and eases the scaling of the site as the organization grows and gets more visitors.
- Radius-based query vars: This plugin adds GeoLocation query vars to WP Query. Using these you can add lat/long and radius values to your query. Adding these to the query will add MySQL at various hooks to run the haversine formula. The results returned are instructors or studios within a specified area.
- WP-REST API: We use this for the front-end directory page, which is a vue app. This is where the GeoLocation runs.
- VueJS: This is used on the main directory page to create a more seamless search experience and also to separate our fron and back-end concerns.
- Modules: These are similar to fron-end JS framework components. Each is re-useable, is specific to one front-end feature, and includes its own PHP, JS, and SCSS file.
- Webpack: Webpack will bundle and test code in the Vue components, plugin-wide scss, and module-specific SCSS/JS
- Autoloaded Namespacing and Classes: This part of the codebase is intended to prevent this plugin from crashing into others. Classes are autoloaded whenever the 'use' statement is applied.
- Template Registration: A page template registration process is included to allow page templates defined in the plugin to be added to the template cache and be used on any page. Single-post templates are added in a similar way.

### To-Do
- Consolidate all admin sidebar links under one main menu
- Add a 'run page creator' workflow to the options page to make the creation ofthe plugins pages optional, as these page templates can be used anywhere.
- Set up an activation hook to request where to place plugin pages.
- Add "ACF required" warning.
- Resetting password should log you in
