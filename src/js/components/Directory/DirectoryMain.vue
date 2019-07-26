<script>
  import 'url-search-params-polyfill'
  import axios from 'axios'
  import Card from './Card'
  import MapView from './MapView'

  export default {
    mounted() {
      // set up the initial results with default params
      this.getCards()
    },
    components: {
      Card,
      MapView
    },
    data() {
      const urlParams = new URLSearchParams(window.location.search)
      return {
        filters: {
          type: 'studio', // post type (instructor or studio)
          keyword: null, // title/name search term
          page: 1, // page #
          per_page: 12, // # of results per page (-1 for maps)
          latitude: null, // used in geolocationqueries
          longitude: null, // used in geolocationqueries
          location: null, // centers the map
          radius: null // location search radius
        },
        params: null,
        showMap: false, // show the map (or not)
        dataLoading: true, // data is loading (or not)
        entries: [], // search results
        totalPages: 0,
        totalPosts: 0,
        errorMsg: null,
      };
    },
    methods: {
      getParams: function () {
        const params = new URLSearchParams(window.location.search)
        for(let filter in this.filters) {
          // check if a filter is set
          if(this.filters[filter]){
            params.append(filter, this.filters[filter])
          }
        }
        return params
      },
      handleSubmit: function () {
        this.params = {
          type: this.filters.type,
          keyword: this.filters.keyword,
          location: this.filters.location,
          latitude: this.filters.latitude,
          longitude: this.filters.longitude,
          radius: this.filters.radius
        }
        if(this.filters.location){
          this.showMap = false
          this.filters.page = 1
          this.filters.radius = (this.filters.radius) ? this.filters.radius : 25
        } else {
          this.showMap = false
          this.filters.radius = null
          this.filters.latitude = null
          this.filters.longitude = null
          this.getCards()
          return
        }
        axios.get(`https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURI(this.filters.location)}.json?access_token=pk.eyJ1Ijoicm9iZXJ0cmFlIiwiYSI6ImNqdndhOHFmejRhczYzeW9qYjJrMGQzY3QifQ.OZag22deq5xysi6cQuAEMw&limit=1`).then(res => {
          console.log('Map Data: ',res.data)
          if(res.data.features.length > 0) {
            this.filters.longitude = res.data.features[0].center[0]
            this.filters.latitude = res.data.features[0].center[1]
            this.getCards()
          } else {
            // handle empty map result set
            this.errorMsg = 'Could not find that location, try again'
          }
        })
      },
      getCards: function () {
        this.dataLoading = true
        this.errorMsg = null
        // this.entries = null
        const params = this.getParams()
        const url = '/wp-json/bd-api/v1/' + this.filters.type + '?' + params.toString()
        console.log('DIR URL: ', url)
        axios.get(url).then(res => {
          console.log('DIR RESPONSE: ', res)
          this.entries = res.data
          this.totalPages = parseInt(res.headers['x-wp-totalpages'])
          this.totalPosts = parseInt(res.headers['x-wp-total'])
          this.dataLoading = false
          if(res.data.length === 0) {
            this.errorMsg = (this.filters.location) ? `Sorry, we couldn't find any results for the location.` : `Sorry, we couldn't find any results for "${this.params.keyword}".<ul><li>We suggest checking your spelling and try again</li></ul></ul>`
          }

          if(this.filters.location) {
            this.showMap = true
          }
        });
      },
      dropdownToggle: function (menu) {
        document.querySelector('#' + menu).classList.toggle('open')
      },
      dropdownSetRadius: function (radius ,menu) {
        this.filters.radius = radius
        document.querySelector('#' + menu).classList.toggle('open')
      },
      typeClick: function (type) {
        this.filters.type = type
      },
      paginationClick: function (pageNum) {
        if(pageNum === this.filters.page) return
        this.filters.page = pageNum
        this.handleSubmit()
        
      },
    }
  };
</script>
<template>
  <div class="directory__wrap">
    <div class="container">
      <form class="directory-loop__filters" @submit.prevent="handleSubmit">
        <div class="filter filter--type">
          <span class="filter--heading">Filter by:</span>
          <label class="filter__label label">
            <div class="radio-wrap">
              <input class="input input__radio" type="radio" name="type[]" value="instructor" v-model="filters.type"/>
              <span class="radio-check"></span>
            </div> Teachers
          </label>
          <label class="filter__label label">
            <div class="radio-wrap">
              <input class="input input__radio" type="radio" name="type[]" value="studio" v-model="filters.type"/>
              <span class="radio-check"></span>
            </div> Studios
          </label>
        </div>
        <div class="filter-divider"></div>
        <div class="filter filter--keyword">
          <label class="filter__label label" for="keyword">Instructor/Studio Name</label>
          <div class="input-icon">
            <input class="input input__text w-100" name="keyword" type="text" v-model="filters.keyword" placeholder="i.e. Lara Heimann"/>
            <div class="icon icon--search contain" />
          </div>
        </div>
        <div class="filter-copy text-center">
          <span class="filter-copy__copy">and/or</span>
        </div>
        <div class="filter filter--city">
          <label class="filter__label label" for="place">City, State or Zip</label>
          <div class="input-icon">
            <input class="input input__text w-100" name="place" type="text" v-model="filters.location" placeholder="Princeton, NJ"/>
            <span class="icon icon--search contain" />
          </div>
        </div>
        <div class="filter filter--radius">
          <label class="filter__label label" for="">Distance</label>
          <div class="dropdown input__select w-100">
            <div v-on:click="dropdownToggle('radius-menu')" class="dropdown-header input-icon">{{(filters.radius) ? filters.radius : '--'}}
              <div class="icon icon--angle-down contain" />
            </div>
            <ul id="radius-menu" class="dropdown-menu" role="menu" aria-label="radius">
              <li role="menuitem" class="dropdown-menu-item" v-on:click="dropdownSetRadius(null, 'radius-menu')">--</li>
              <li role="menuitem" class="dropdown-menu-item" v-on:click="dropdownSetRadius(5, 'radius-menu')">5</li>
              <li role="menuitem" class="dropdown-menu-item" v-on:click="dropdownSetRadius(10, 'radius-menu')">10</li>
              <li role="menuitem" class="dropdown-menu-item" v-on:click="dropdownSetRadius(25, 'radius-menu')">25</li>
              <li role="menuitem" class="dropdown-menu-item" v-on:click="dropdownSetRadius(50, 'radius-menu')">50</li>
              <li role="menuitem" class="dropdown-menu-item" v-on:click="dropdownSetRadius(100, 'radius-menu')">100</li>
            </ul>
          </div>
        </div>
        <div class="filter filter--search">
          <label class="filter__label label hidden" for="submit-search">Click to Search</label>
          <button type="submit" name="submit-search" class="button input input__submit w-100">Search</button>
        </div>
      </form>
    </div>
    <div class="directory__results">
      <div class="container">
        <div class="directory__errors" v-if="errorMsg">
          <span class="label">No Results Found</span>
          <span class="error-msg" v-html="errorMsg"></span>
        </div>
        <div v-if="this.dataLoading" class="directory__loader">
          <span class="directory__loader-message">Loading Profiles...</span>
        </div>
        <map-view
          v-if="showMap && this.entries.length > 0"
          :entries="entries"
          :location="params.location"
          :centerLat="filters.latitude"
          :centerLng="filters.longitude"
          :type="params.type"
        />
        <div class="directory__cards" v-if="this.entries.length > 0">
          <card
            v-for="(entry, i) in this.entries"
            :key="i"
            :title="entry.title"
            :image="entry.image"
            :certifications="(entry.certification) ? entry.certification : []"
            :meta="entry.acf"
            :row="entry.row"
            :email="entry.row.contact_info_email"
            :route="entry.permalink"
            :type="entry.row.post_type"
            :class="'card' + ' card--' + entry.row.post_type"
          />
        </div>
        <div class="directory__pagination container" v-if="!filters.location && this.entries.length > 0">
          <span class="directory__pagination-link link--prev" v-if="filters.page > 1" v-on:click="paginationClick(filters.page - 1)">Previous</span>
          <ul v-if="totalPages > 2">
            <li v-for="(n,i) in totalPages" :key="i" :class="{ active: filters.page === n }" class="directory__pagination-link" v-on:click="paginationClick(n)"> {{n}} </li>
          </ul>
          <span class="directory__pagination-link link--next" v-if="filters.page < totalPages" v-on:click="paginationClick(filters.page + 1)">Next</span>
        </div>
      </div>
    </div>
  </div>
</template>