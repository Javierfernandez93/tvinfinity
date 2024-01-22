import { User } from '../../src/js/user.module.js?v=2.1.9'   

const AllmoviesViewer = {
    name : 'allmovies-viewer',
    data() {
        return {
            User: new User,
            query: null,
            moviesAux: null,
            movies: null
        }
    },
    watch : {
        query : {
            handler() {
                this.filterData()
            },
            deep: true
        }
    },
    methods: {
        filterData() {
            this.movies = this.moviesAux

            this.movies = this.movies.filter((movie) => {
                return movie.title.toLowerCase().includes(this.query.toLowerCase()) 
            })
        },
        getMovies(filter) {
            return new Promise((resolve,reject) => {
                this.User.getMovies({filter:filter}, (response) => {
                    if (response.s == 1) {
                        resolve(response.movies)
                    }

                    reject()
                })
            })
        },
        viewMovie(movie)
        {
            window.location.href = `../../apps/movies/watch?mid=${movie.movie_id}`
        },
        _getMovies()
        {
            let filter = null

            if(this.filter)
            {
                filter = JSON.parse(this.filter)
            }

            this.getMovies(filter).then((movies) => {
                this.moviesAux = movies
                this.movies = this.moviesAux
            }).catch(() => this.movies = false)
        }
    },
    mounted() {
        this._getMovies()
    },
    template : `
        <div v-if="movies">
            <div class="card mb-3">
                <div class="input-group flex-nowrap bg-dark">
                    <span class="input-group-text bg-dark px-3 text-white" id="addon-wrapping"><i class="bi fs-5 bi-search"></i></span>
                    <input :autofocus="true" type="text" v-model="query" class="form-control text-white fs-4 px-3 form-control-lg bg-transparent" placeholder="Buscar pelicula por nombre" aria-label="Buscar pelicula por nombre" aria-describedby="addon-wrapping">
                </div>
            </div>

            <div class="mb-3">
                <h3 class="text-white">
                    {{title}}
                </h3>
                <div>Total {{movies.length}} peliculas</div>
            </div>
        
            <div class="row">
                <div v-for="movie in movies" class="col-12 col-md-4 col-xl-3 mb-5 overflow-hidden">
                    <div @click="viewMovie(movie)" class="card card-movie-outside cursor-pointer f-zoom-element-sm bg-transparent" 
                        :style="{'background-image':'url('+movie.image+')'}">
                        <div class="card-body">
                            <div class="row card-movie align-content-end">
                                <div class="col-12 text-truncate">
                                    <h4 class="text-white">{{movie.title}}</h4>
                                        <h5 class="text-light">({{movie.year}})</h5>
                                    <div>
                                    <span v-for="gender in movie.genders" class="badge bg-secondary me-2">
                                        {{gender.gender}}
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { AllmoviesViewer } 