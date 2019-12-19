<script>
  export default {
    props: {
      entries: Object,
      location: String,
      centerLat: Number,
      centerLng: Number,
      type: String
    },
    data () {
      return {
        entries: this.entries,
        map: null,
        key: theme_vars.mapbox.key,
        style: theme_vars.mapbox.style,
        container: 'map-display',
        zoom: 14,
        mapLoading: true,
        mapCenter: [
          this.centerLng,
          this.centerLat
        ],
        markerJSON: [],
        buttonCopy: 'Close Map',
        type: this.type
      }
    },
    beforeMount () {
      // get map data ready
      this.getMarkerJSON()
    },
    mounted () {
      this.buildMap()
      this.buildMarkers()
      this.fitMap()
    },
    methods: {
      getMarkerJSON: function () {
        let features = []
        let properties = {}
        const geoJSON = {
          type: 'FeatureCollection',
          features: []
        }

        for(let entry of this.entries){
          const feature = {
            type: 'Feature',
            geometry: {
              type: 'Point',
              coordinates: [entry.row.longitude, entry.row.latitude]
            },
            properties: {}
          }
          if(this.type ==='instructor') {
            properties = {
              title: entry.title,
              subhead: 'Email',
              email: entry.row.contact_info_email,
              permalink: entry.permalink,
              slug: entry.slug
            }
          } else {
            properties = {
              title: entry.title,
              subhead: 'Address',
              address1: entry.row.address_street,
              address2: `<span class="capitalize">${entry.row.address_city}</span>, ${entry.row.address_state} ${entry.row.address_postal_code}`,
              permalink: entry.permalink,
              slug: entry.slug
            }
          }
          feature.properties = properties
          features.push(feature)
        }

        geoJSON.features = features
        this.markerJSON = geoJSON
      },
      buildMap: function () {
        mapboxgl.accessToken = this.key
        this.map = new mapboxgl.Map({
          container: this.container,
          center: this.mapCenter,
          style: this.style,
          zoom: this.zoom
        })
      },
      buildMarkers: function () {
        // 'this' means something else in the loop below
        const map = this.map
        const type = this.type
        const container = this.container

        // add markers with popups and events to map
        this.markerJSON.features.forEach(function(marker) {
          
          // create a HTML element for each feature
          let el = document.createElement('div')
          el.className = `marker marker--${marker.properties.slug}`
          el.id = `marker-${marker.properties.slug}`
          el.setAttribute('data-slug', marker.properties.slug)

          // add copy for each of the two post types
          let copy = `<div id="popup-${marker.properties.slug}" class="popup-data" data-slug="${marker.properties.slug}"><h4 class="popup-data__head">${marker.properties.title}</h4> <h5 class="popup-data__subhead">${marker.properties.subhead}</h5>`
          copy += (type === 'instructor') ? `<a href="mailto:${marker.properties.email}">${marker.properties.email}</a><br><a href="${marker.properties.permalink}" class="popup-data__link">View Full Bio</a></div>` : `<p>${marker.properties.address1}<br>${marker.properties.address2}</p><a href="${marker.properties.permalink}" class="popup-data__link">More Info</a></div>`
          
          // create the popup
          const popup = new mapboxgl.Popup({
            offset: 30,
            closeButton: false
          }).setHTML(copy)

          // make a marker for each feature and add to the map
          new mapboxgl.Marker(el)
            .setLngLat(marker.geometry.coordinates)
            .setPopup(popup)
            .addTo(map)

          // attach click event to marker
          el.addEventListener('click', (e) => {
            // e.stopPropagation()
            const markers = document.querySelectorAll('.marker')
            markers.forEach(function(marker) {
              marker.classList.remove('active')
            })
            e.target.classList.add('active')
          })
        })

        document.querySelector(`#${container}`).addEventListener( 'click', function(e) {
          if(e.target.className === 'mapboxgl-canvas'){
            const markers = document.querySelectorAll('.marker')
            markers.forEach(function(marker) {
              marker.classList.remove('active')
            })
          }
        })

        this.map = map
      },
      fitMap: function () {
        const bounds = new mapboxgl.LngLatBounds()

        this.markerJSON.features.forEach(function(feature) {
            bounds.extend(feature.geometry.coordinates);
        })

        this.map.fitBounds(bounds, {
          padding: 30,
          maxZoom: 15
        })
      },
      mapButtonClick: function () {
        if(this.buttonCopy === 'Open Map'){
          this.buttonCopy = 'Close Map'
          document.querySelector('#' + this.container).classList.remove('closed')
        } else {
          this.buttonCopy = 'Open Map'
          document.querySelector('#' + this.container).classList.add('closed')
        }
      }
    }
  }
</script>
<template>
  <div class="map">
    <div class="map__header">
      <div class="map-header--info">
        <span class="label">Search Results</span><br>
        <span>{{entries.length}} {{(entries.length > 1) ? 'search results' : 'search result'}} for {{type}}s in {{location}}</span>
      </div>
      <div class="map-header--clear">
        <button class="button button--white" v-on:click="mapButtonClick()"><span class="icon contain icon--map"></span> {{buttonCopy}}</button>
      </div>
    </div>
    <div id="map-display" class="map__results" :class="'map-type--' + type"></div>
  </div>
</template>