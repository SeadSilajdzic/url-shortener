<template>
    <form @submit.prevent="shortenUrl" id="shortenUrlForm">
        <div>
            <input v-model="url" type="url" name="url" required placeholder="Enter URL">
            <p v-show="hasErrors">{{ urlError }}</p>
            <p class="shorten-paragraph">
                Shorten URL: {{ shortenURL }}
                <small v-show="shortenURL !== ''" class="copy-shortenUrl" @click="copyURL(shortenURL)">Copy</small>
            </p>
            <button type="submit">Shorten URL</button>
        </div>
    </form>
</template>

<script>
    import axios from "axios";
    export default {
        data() {
            return {
                url: '',
                hasErrors: false,
                urlError: '',
                shortenURL: ''
            }
        },
        methods: {
            shortenUrl() {
                axios.post(window.location.pathname . substring(0, window.location.pathname.length - "/generate".length) + '/shorten', {url: this.url})
                    .then(response => {
                        this.hasErrors = false
                        this.urlError = ''
                        this.shortenURL = response.data.shortenURL
                    })
                    .catch((error) => {
                        if(error) {
                            console.log(error)
                            this.hasErrors = true
                            this.urlError =  error.response.data.error.url[0]
                        }
                    })
            },

            async copyURL(shortenURL) {
                try {
                    await navigator.clipboard.writeText(shortenURL);
                    let copyBtn = document.querySelector('.copy-shortenUrl')
                    copyBtn.classList.add('active')

                    setTimeout(() => {
                        copyBtn.classList.remove('active')
                    }, 750)
                } catch($e) {
                    alert('Cannot copy');
                }
            }
        }
    }
</script>

<style scoped>
    form {
        width: 600px;
    }

    form input {
        width: 100%;
        line-height: 30px;
        border: 1px solid black;
        border-radius: 5px;
        padding: 5px;
        font-size: 18px;
    }

    .copy-shortenUrl {
        cursor: pointer;
        position: relative;
    }

    .copy-shortenUrl::before {
        content: 'Copied';
        position: absolute;
        left: 0;
        top: 0;
        opacity: 0;
        z-index: 2;
        transition: top .3s ease-out;
    }

    .copy-shortenUrl.active::before {
        top: -100%;
        opacity: 1;
        pointer-events: none;
    }

    .shorten-paragraph {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>
