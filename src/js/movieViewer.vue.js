import { User } from '../../src/js/user.module.js?v=2.1.9'   

const MovieViewer = {
    name : 'movie-viewer',
    props : ['title'],
    data() {
        return {
            User: new User,
            movie: null
        }
    },
    methods: {
        getMovie(movie_id) {
            return new Promise((resolve,reject) => {
                this.User.getMovie({movie_id:movie_id}, (response) => {
                    if (response.s == 1) {
                        resolve(response.movie)
                    }

                    reject()
                })
            })
        },
        addViewPerMovie(movie_id) {
            return new Promise((resolve,reject) => {
                this.User.addViewPerMovie({movie_id:movie_id}, (response) => {
                    if (response.s == 1) {
                        resolve(response.movie)
                    }

                    reject()
                })
            })
        },
        play()
        {
            setTimeout(()=>{
                var options = {
                    responsive: true,
                    aspectRatio: "16:9"
                };
                
                var player = videojs(document.getElementById('myPlayer'), options, function onPlayerReady() {
                    videojs.log('Your player is ready!');
                
                    this.play();
                        
                    this.on('ended', function() {
                        videojs.log('Awww...over so soon?!');
                    });
                })
            },1000)
        },
        viewMovie(movie)
        {
            window.location.href = `../../apps/movies/watch?mid=${movie.movie_id}`
        },
    },
    mounted() {
        this.getMovie(getParam("mid")).then((movie) => {
            this.addViewPerMovie(getParam("mid")).then(() => {
                this.movie = movie
            })
        }).catch(() => this.movie = false)
    },
    template : `
        <div v-if="movie">
            <div class="mb-3">
                <h3 class="text-white">
                    {{movie.title}} <span v-if="movie.year">{{movie.year}}</span>
                </h3>
            </div>
            <div v-if="movie.player">
                {{play()}}
                <video
                    id="myPlayer"
                    class="video-js"
                    controls
                    preload="auto"
                    :poster="movie.image"
                    data-setup="{}">

                    <source :src="movie.link" type="video/mp4" />
                    
                    <p class="vjs-no-js">
                        To view this video please enable JavaScript, and consider upgrading to a web browser that
                        <a href="https://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
                    </p>
                </video>
            </div>
            <div v-else>
                <iframe :src="movie.link" width="100%" height="980" allow="autoplay"></iframe>
            </div>
        </div>
    `,
}

export { MovieViewer } 