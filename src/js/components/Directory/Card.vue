<script>
  export default {
    props: {
      title: String,
      email: String,
      image: String,
      route: String,
      certifications: {
        type:Array,
        default: []
      },
      type: String,
      row: Object
    },
    methods: {
      getSVG: function (name) {
        return window.location.protocol + '//' + window.location.hostname + '/wp-content/plugins/barrel-directory/src/svg/' + name
      },
      displaySiteURL: function(url = null){
        if(!url) {
          return null
        }

        url = url.replace('https://', '')
        url = url.replace('http://', '')
        url = url.replace('www.', '')
        url = url.replace('/', '')
        return url
      },
      displayPhone: function(phone = null){
        if(!phone) {
          return null
        }
        
        const allowed = ['0','1','2','3','4','5','6','7','8','9']
        let displayStr = ''
        for (var i = 0; i < phone.length; i++) {
          let currentChar = phone.charAt(i)
          // exclude char if its the first one and is a '1'
          if(i === 0 && currentChar == 1) continue
          if(allowed.indexOf(currentChar) != -1){
            displayStr += currentChar
          }
        }
        if(displayStr.length === 10){
          const areaCode = displayStr.substring(0,3)
          const part2 = displayStr.substring(3,6)
          const part3 = displayStr.substring(6,10)
          displayStr = `${areaCode}.${part2}.${part3}`
        }
        if(displayStr.length === 7){
          const part2 = displayStr.substring(0,3)
          const part3 = displayStr.substring(3,7)
          displayStr = `${part1}.${part2}`
        }
        return displayStr
      }
    }
  }
</script>
<template>
  <div>
    <div class="card__inner">
      <div class="card__image" v-if="image" :style="'background-image: url(' + image + ')'" lang="en">
      </div>
      <div class="card__image placeholder-image placeholder-image--contain" v-else></div>
      <div class="card__heading card--item">
        <h2 class="card__title">{{title}}<br>
          <span class="card__position label">{{type}}</span>
        </h2>
        <div class="card__social-icons">
          <a v-if="row.social_media_facebook_profile" :href="row.social_media_facebook_profile" class="card__social-link" target="_blank">
            <span class="icon icon--facebook contain"></span>
          </a>
          <span class="card__empty" v-else>&nbsp;</span>
          <a v-if="row.social_media_instagram_profile" :href="row.social_media_instagram_profile" class="card__social-link" target="_blank">
            <span class="icon icon--instagram contain"></span>
          </a>
          <span class="card__empty" v-else>&nbsp;</span>
          <a v-if="row.social_media_twitter_profile" :href="row.social_media_twitter_profile" class="card__social-link" target="_blank">
            <span class="icon icon--twitter contain"></span>
          </a>
          <span class="card__empty" v-else>&nbsp;</span>
          <a v-if="row.social_media_youtube_profile" :href="row.social_media_youtube_profile" class="card__social-link" target="_blank">
            <span class="icon icon--youtube contain"></span>
          </a>
          <span class="card__empty" v-else>&nbsp;</span>
        </div>
      </div>
      <div class="card__details card__details--instructor" v-if="type === 'instructor'">
        <div class="card__location card--item">
          <h4 class="card-item__label label">Location</h4>
          <span class="card__empty" v-if="!row.address_city && !row.address_state">N/A</span>
          <span v-else class="paragraph card__location--city">{{row.address_city}}</span>, <span class="paragraph card__location--state">{{row.address_state}}</span>
        </div>
        <div class="card__contact card--item">
          <h4 class="card-item__label label">Contact</h4>
          <span class="card__empty" v-if="!email">N/A</span>
          <a v-else :href="'mailto:' + email">{{email}}</a>
        </div>
        <div class="card__certifications card--item">
          <h4 class="card-item__label label">LYT Certifications</h4>
          <div class="card__certification-icons">
            <span class="card__empty" v-if="certifications.length === 0">N/A</span>
            <div v-if="certifications.length > 0" v-for="(cert, i) in certifications" :key="i" :class="'icon icon--' + cert.slug + ' contain'" />
          </div>
        </div>
      </div>
      <div class="card__details card__details--studio" v-if="type === 'studio'">
        <div class="card__address card--item">
          <h4 class="card-item__label label">Address</h4>
          <span class="paragraph">{{row.address_street}} {{row.address_apt}}<br>
          {{row.address_city}}, {{row.address_state}} {{row.address_zip}}</span>
        </div>
        <div class="card__site card--item" v-if="row.contact_info_website">
          <h4 class="card-item__label label">Website</h4>
          <span class="card__empty" v-if="!row.contact_info_website">N/A</span>
          <a class="paragraph" :href="row.contact_info_website" target="_blank">{{displaySiteURL(row.contact_info_website)}}</a>
        </div>
        <div class="card__email card--item" v-if="row.contact_info_email" lang="en">
          <h4 class="card-item__label label">Email</h4>
          <span class="card__empty" v-if="!row.contact_info_email">N/A</span>
          <a class="paragraph" :href="'mailto:' + row.contact_info_email">{{row.contact_info_email}}</a>
        </div>
        <div class="card__phone card--item" v-if="row.contact_info_phone">
          <h4 class="card-item__label label">Phone</h4>
          <span class="card__empty" v-if="!row.contact_info_phone">N/A</span>
          <a class="paragraph" :href="'tel:+1' + row.contact_info_phone"><span class="">{{displayPhone(row.contact_info_phone)}}</span></a>
        </div>
        <div class="card__instructors card--item" v-if="row.basic_info_lyt_instructors.length > 0">
          <h4 class="card-item__label label">Current LYT-Certified Teachers</h4>
          <a class="paragraph" :href="instructor.permalink" v-for="(instructor, i) in row.basic_info_lyt_instructors" :key="i">{{instructor.title}}<span v-if="i < row.basic_info_lyt_instructors.length - 1">, </span></a>
        </div>
      </div>
      <div class="card__link">
        <a :href="route" class="button">{{(type === 'studio') ? 'View More Info' : 'View Full Bio'}}</a>
      </div>
    </div>
  </div>
</template>