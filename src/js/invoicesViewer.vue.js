import { User } from '../../src/js/user.module.js?v=2.1.9'   

const InvoicesViewer = {
    name : 'invoices-viewer',
    data() {
        return {
            User: new User,
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
        getInvoices() {
            return new Promise((resolve, reject) => {
                this.User.getInvoices({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.invoices)
                    }

                    reject()
                })
            })
        },
    },
    mounted() {
        this.getInvoices().then((invoices) => {
            this.invoices = invoices
            this.invoicesAux = invoices
        }).catch((err) => { this.invoices = false })
    },
    template : `
        <div v-if="invoices">
            <div class="card">
                <div class="card-header bg-light">
                    <div class="row align-items-center">
                        <div class="col fw-semibold text-dark">Mis ordenes de compra</div>
                        <div class="col-auto"><span class="badge text-dark">Total {{invoices.length}}</span></div>
                    </div>
                </div>
                <div class="card-header">
                    <div class="row">
                        <div class="col">
                            <input type="search" class="form-control" v-model="query" placeholder="buscar por monto o items"/>
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
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
                                        <a v-if="invoice.status == STATUS.PENDING"
                                            :href="invoice.checkout_data.checkout_url"
                                            :disabled="invoice.status != STATUS.PENDING"
                                            target="_blank"
                                            class="btn btn-sm shadow-none m-0 btn-success"
                                            >
                                            Pagar
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div v-else="invoices == false" class="alert alert-light text-center">
            <div><strong>Importante</strong></div>
            Aquí te mostraremos las compras que realices tanto de licencias como de activación y mensualidad de tu franquicia.

            <div class="d-flex justify-content-center py-3">
                <button class="btn btn-primary me-2 mb-0 shadow-none">Realizar activación</button>
                <button class="btn btn-primary me-2 mb-0 shadow-none">Realizar compra mensual</button>
                <button class="btn btn-primary mb-0 shadow-none">Comprar licencias extras</button>
            </div>
        </div>
    `,
}

export { InvoicesViewer } 