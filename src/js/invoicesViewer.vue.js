import { User } from '../../src/js/user.module.js?v=2.1.9'   

const InvoicesViewer = {
    name : 'invoices-viewer',
    data() {
        return {
            User: new User,
            busy: null,
            query: null,
            invoices: null,
            invoicesAux: null,
            STATUS : {
                DELETED: -1,
                CANCELED: 0,
                PENDING: 1,
                PAYED: 2,
                REFUND: 3,
            }
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
            this.invoices = this.invoicesAux

            this.invoices = this.invoices.filter((invoice) => {
                return invoice.amount.toString().includes(this.query) || invoice.invoice_id.toLowerCase(this.query.toLowerCase())
            })
        },
        openFileManager(buy_per_user_id) 
        {
            $("#"+buy_per_user_id).click()
        },
        uploadValidationData(invoice) 
        {
            this.User.uploadValidationData({invoice:invoice},(response)=>{
                if(response.s == 1)
                {
                    toastInfo({
                        message: 'Comprobante subido, validaremos tu compra a la brevedad',
                    })   
                }
            })
        },
        uploadFile(target,buy_per_user_id) 
        {
            let files = $(target).prop('files');
            var form_data = new FormData();
          
            form_data.append("file", files[0]);
          
            this.User.uploadPaymentImage(form_data,$(".progress").find(".progress-bar"),(response)=>{
                if(response.s == 1)
                {
                    this.invoices = this.invoices.map((invoice)=>{
                        if(invoice.buy_per_user_id == buy_per_user_id)
                        {
                            invoice.validation_data = {
                                image: response.target_path
                            }

                            this.uploadValidationData(invoice)
                        }

                        return invoice
                    })

                    console.log(this.invoices)
                }
            });
        },
        getInvoices() {
            this.invoices = null
            this.invoicesAux = null
            this.busy = true    
            this.User.getInvoices({}, (response) => {
                this.busy = false
                if (response.s == 1) {
                    this.invoices = response.invoices
                    this.invoicesAux = response.invoices
                } else {
                    this.invoices = false
                    this.invoicesAux = false
                }
            })
        },
    },
    mounted() {
        this.getInvoices()
    },
    template : `
        
        <div>
            <div class="card">
                <div class="card-header bg-transparent">
                    <div class="row align-items-center">
                        <div class="col">
                            <span v-if="invoices" class="badge text-dark">Total {{invoices.length}}</span>
                            <div class="h3 mb-0">Mis compras</div>
                        </div>
                     
                        <div class="col">
                            <input type="search" class="form-control" v-model="query" placeholder="buscar por monto o items"/>
                        </div>
                    </div>
                </div>
                <div v-if="busy" class="d-flex justify-content-center py-5">
                    <div class="spinner-grow" style="width: 1rem; height: 1rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            
                <div v-if="invoices" class="table-responsive">
                    <table class="table">
                        <thead class="thead-light">
                            <tr class="text-xs text-center text-secondary">
                                <th class="text-uppercase">
                                    ID
                                </th>
                                <th class="text-uppercase">
                                    Items
                                </th>
                                <th class="text-uppercase">
                                    Fecha
                                </th>
                                <th class="text-uppercase">
                                    Estatus
                                </th>
                                <th class="text-uppercase">
                                    Método de pago
                                </th>
                                <th class="text-uppercase">
                                    TOTAL
                                </th>
                                <th>
                                    
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="invoice in invoices" class="align-middle text-xs text-center">
                                <td class="text-secondary fw-semibold">
                                    #{{invoice.invoice_id}}
                                </td>
                                <td class="text-dark fw-semibold">
                                    <span v-for="(item,index) in invoice.items" class="text-primary">
                                        {{item.title}} {{index < invoice.items.length -1 ? ',' : ''}}
                                    </span>
                                </td>
                                <td class="text-dark fw-semibold">
                                    <span class="my-2 text-xs">{{invoice.create_date.formatFullDate()}}</span>
                                </td>
                                <td class="text-dark fw-semibold">
                                    <span v-if="invoice.status == STATUS.DELETED"
                                        class="badge border border-danger text-danger">
                                        <i class="bi bi-dash-circle-fill"></i>
                                        Eliminada
                                    </span>
                                    <span v-else-if="invoice.status == STATUS.CANCELED"
                                        class="badge border border-warning text-warning">
                                        <i class="bi bi-dash-circle"></i>
                                        Cancelada
                                    </span>
                                    <span v-else-if="invoice.status == STATUS.PENDING"
                                        class="badge border border-secondary text-secondary">
                                        <i class="bi bi-clock"></i>
                                        Pendiente
                                    </span>
                                    <span v-else-if="invoice.status == STATUS.PAYED"
                                        class="badge border border-success text-success">
                                        <i class="bi bi-check-circle"></i> 
                                        Pagada
                                    </span>
                                    <span v-else-if="invoice.status == STATUS.REFUND"
                                        class="badge border border-primary text-primary">
                                        <i class="bi bi-arrow-clockwise"></i>
                                        Reembolsada
                                    </span>
                                </td>
                                <td class="text-dark fw-semibold">
                                    <span class="badge bg-gradient-primary">{{invoice.catalog_payment_method.payment_method}}</span>
                                </td>
                                <td class="text-dark fw-semibold">
                                    <span class="my-2 text-xs">$ {{invoice.amount.numberFormat(2)}}</span>
                                </td>
                                <td class="text-dark fw-semibold">
                                    
                                    <div v-if="invoice.checkout_data.checkout_url">
                                        <div v-if="invoice.status == STATUS.PENDING">
                                            <input class="opacity-0 d-none cursor-pointer bg-dark w-100 h-100 start-0 top-0 position-absolute" :id="invoice.buy_per_user_id" @change="uploadFile($event.target,invoice.buy_per_user_id)" capture="filesystem" type="file" accept=".jpg, .png, .jpeg" />

                                            <button v-if="invoice.catalog_payment_method_id == 7" @click="openFileManager(invoice.buy_per_user_id)" class="btn btn-sm me-2 shadow-none m-0 btn-primary">
                                                <span v-if="invoice.validation_data">
                                                    Cambiar comprobante
                                                </button>
                                                <span v-else>
                                                    Subir comprobante
                                                </button>
                                            </button>
                                            <a
                                                :href="invoice.checkout_data.checkout_url"
                                                :disabled="invoice.status != STATUS.PENDING"
                                                target="_blank"
                                                class="btn btn-sm shadow-none m-0 btn-success"
                                                >
                                                Pagar
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-else-if="invoices == false" class="alert alert-light text-center">
                    <div><strong>Importante</strong></div>
                    Aquí te mostraremos las compras que realices tanto de licencias como de activación y mensualidad de tu franquicia.

                    <div class="d-flex justify-content-center py-3">
                        <button class="btn btn-primary me-2 mb-0 shadow-none">Realizar activación</button>
                        <button class="btn btn-primary me-2 mb-0 shadow-none">Realizar compra mensual</button>
                        <button class="btn btn-primary mb-0 shadow-none">Comprar licencias extras</button>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { InvoicesViewer } 