import { User } from '../../src/js/user.module.js?v=2.1.9'   

const ToptenViewer = {
    name : 'topten-viewer',
    data() {
        return {
            User : new User,
            movies : null
        }
    },
    methods: {
        getLastTopTenMovies() {
            return new Promise((resolve) => {
                this.User.getLastTopTenMovies({},(response)=>{
                    if(response.s == 1)
                    {
                        resolve(response.movies)
                    }
                })
            })
        }
    },
    mounted() 
    {   
        this.getLastTopTenMovies().then((movies)=>{
            this.movies = movies
        })
    },
    template : `
        <div v-show="movies" class="mb-3">
            <p>Últimas 10 peliculas añadidas en IPTV</p>

            <div class="row">
                <div v-for="movie in movies" class="col-12 col-xl">
                    <a :href="movie.link" target="_blank">
                        <img :src="movie.image" class="img-fluid" title="movie"/>
                    </a>
                </div>
            </div>
        </div>
    `,
}

export { ToptenViewer } 