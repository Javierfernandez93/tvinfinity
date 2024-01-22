import { User } from '../../src/js/user.module.js?v=2.1.9'   

const StoreitemsViewer = {
    name : 'storeitems-viewer',
    props : ['cart','hasitems'],
    emits: ['nextstep'],
    data() {
        return {
            User: new User,
            items: null,
        }
    },
    watch : {
        items : {
            handler() {
                this.cart.hasItems = this.getHasItems()
            },
            deep: true
        }
    },
    methods: {
        getHasItems() {
            let hasItems = false
                
            if(this.items.length > 0) 
            {
                this.items.map((item)=>{
                    if(item.selected)
                    {
                        hasItems = true
                    }
                })
            }

            return hasItems
        },
        filterData() {
            this.vcards = this.vcardsAux

            this.vcards = this.vcards.filter(vcard => vcard.title.toLowerCase().includes(this.query.toLowerCase()))
        },
        addPackage(item) {
            this.User.addPackage({package_id:item.package_id}, (response) => {
                if (response.s == 1) {
                    this.cart.package_id = response.package_id
                    
                    item.selected = true

                    setTimeout(()=>{
                        this.$emit('nextstep')
                    },500)
                }
            })
        },
        deleteItem(item)
        {
            this.User.deleteItem({id:item.package_id}, (response) => {
                if (response.s == 1) {
                    item.selected = false
                }
            })
        },
        getStoreItems() {
            this.User.getStoreItemsPackage({package_type:this.cart.package_type}, (response) => {
                if (response.s == 1) {
                    this.items = response.items
                }
            })
        },
    },
    mounted() {
        this.getStoreItems()
    },
    template : `
        <div v-if="items">
            <ul class="list-group">
                <li v-for="item in items" class="list-group-item list-group-item-action f-zoom-element-sm py-5">
                    <div class="row align-items-center" :class="!item.aviable ? 'opacity-50' : ''">
                        <div class="col-auto">
                            <div class="avatar avatar-xl rounded-circle">
                                <img :src="item.image" class="avatar avatar-xl rounded-circle">
                            </div>
                        </div>

                        <div class="col">
                            <div class="text-dark">
                                <span class="h4">{{item.title}}</span>
                                <span class="badge bg-danger ms-2" v-if="item.offer">OFERTA Limitada</span>
                            </div>

                            {{item.description}}

                            <div v-if="item.products" class="mt-3">
                                <div v-for="product in item.products">
                                    <p class="fw-semibold mb-0 text-dark">
                                        {{product.quantity}}
                                        {{product.title}}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-auto" v-if="item.aviable">
                            <span class="text-end">
                                <div>Precio</div>
                                <div class="h3">$ {{item.amount.numberFormat(2)}} MXN</div>
                            </span>
                        </div>
                        <div class="col-auto" v-if="item.aviable">
                            
                            <div class="row">
                                <div class=""
                                    :class="item.selected ? 'col-9': 'col'">
                                    <button 
                                        @click="addPackage(item)"
                                        :class="item.selected ? 'btn-light' : 'btn-dark'"
                                        v-text="item.selected ? 'Elegido' : 'Comprar'"
                                        class="btn w-100 btn-lg shadow-none col mb-0"></button>
                                </div>
                                <div class="col-3" v-if="item.selected">
                                    <button 
                                        @click="deleteItem(item)"
                                        class="btn col w-100 shadow-none btn-danger mb-0"><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    `,
}

export { StoreitemsViewer } 