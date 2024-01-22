import { User } from '../../src/js/user.module.js?v=2.1.9'   

const GainsViewer = {
    name : 'gains-viewer',
    data() {
        return {
            User: new User,
            profits: null,
            totals: {
                amount: 0
            },
            columns: { // 0 DESC , 1 ASC 
                create_date: {
                    name: 'create_date',
                    desc: false,
                },
                profit: {
                    name: 'profit',
                    desc: true,
                },
                name: {
                    name: 'name',
                    desc: true,
                    alphabetically: true,
                },
            },
            STATUS: {
                PENDING_FOR_DISPERSION: 1,
                COMPLETED: 2,
            },
            COMMISSION_TYPES: {
                NETWORK: 1,
                GROUP: 2,
            }
        }
    },
    methods: {
        sortData(column) {
            this.profits.sort((a, b) => {
                const _a = column.desc ? a : b
                const _b = column.desc ? b : a

                if (column.alphabetically) {
                    return _a[column.name].localeCompare(_b[column.name])
                } else {
                    return _a[column.name] - _b[column.name]
                }
            });

            column.desc = !column.desc
        },
        calculateTotals() {
            if(this.profits.length > 0)
            {
                this.profits.map((profit) => {
                    this.totals.amount += profit.amount ? parseFloat(profit.amount) : 0;
                })
            }
        },
        getProfits() {
            return new Promise((resolve,reject) => {
                this.User.getProfits({}, (response) => {
                    if (response.s == 1) {
                        resolve(response.profits)   
                    }
                    reject()
                })
            })
        },
    },
    mounted() {
        this.getProfits().then((profits) => {
            this.profits = profits

            this.calculateTotals()
        }).catch(() => this.profits = false)
    },
    template : `
        <div class="row">
            <div class="col-12">
                <div v-if="profits"
                    class="card mb-4 overflow-hidden border-radius-xl">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col fw-semibold text-dark">Comisiones</div>
                            <div class="col-auto"><span class="badge bg-primary">Total de comisiones {{profits.length}}</span></div>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-0 pb-2">
                        <div class="table-responsive p-0">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr class="text-center">
                                        <th @click="sortData(columns.create_date)" class="text-center c-pointer text-uppercase text-xxs text-primary font-weight-bolder opacity-7">
                                            <span v-if="columns.create_date.desc">
                                                <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                            </span>    
                                            <span v-else>    
                                                <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                            </span>    
                                            <u class="text-sm ms-2">Usuario</u>
                                        </th>
                                        <th @click="sortData(columns.create_date)" class="text-center c-pointer text-uppercase text-xxs text-primary font-weight-bolder opacity-7">
                                            <span v-if="columns.create_date.desc">
                                                <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                            </span>    
                                            <span v-else>    
                                                <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                            </span>    
                                            <u class="text-sm ms-2">Fecha</u>
                                        </th>
                                        <th @click="sortData(columns.profit)" class="text-center c-pointer text-uppercase text-xxs text-primary font-weight-bolder opacity-7">
                                            <span v-if="columns.profit.desc">
                                                <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                            </span>    
                                            <span v-else>    
                                                <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                            </span>    
                                            <u class="text-sm ms-2">Monto</u>
                                        </th>
                                        <th @click="sortData(columns.name)" class="text-center c-pointer text-uppercase text-xxs text-primary font-weight-bolder opacity-7">
                                            <span v-if="columns.name.desc">
                                                <i class="bi text-primary bi-arrow-up-square-fill"></i>
                                            </span>    
                                            <span v-else>    
                                                <i class="bi text-primary bi-arrow-down-square-fill"></i>
                                            </span>    
                                            <u class="text-sm ms-2">Tipo</u>
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Estatus</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="profit in profits" class="text-center">
                                        <td>
                                            <p class="text-xs text-secondary mb-0">{{profit.names}}</p>
                                        </td>
                                        <td>
                                            <p class="text-xs text-secondary mb-0">{{profit.create_date.formatDate()}}</p>
                                        </td>
                                        <td>
                                            <p class="fw-semibold">
                                            $ {{profit.amount.numberFormat(2)}} {{profit.currency}}
                                            </p>
                                        </td>
                                        <td>
                                            <span v-if="profit.catalog_commission_type_id == COMMISSION_TYPES.NETWORK" class="badge bg-success">
                                                Frontal
                                            </span>
                                            <span v-if="profit.catalog_commission_type_id == COMMISSION_TYPES.GROUP" class="badge bg-primary">
                                                GRUPO
                                            </span>
                                        </td>
                                        <td>
                                            <span v-if="profit.status == STATUS.PENDING_FOR_DISPERSION" class="badge bg-warning">
                                                Pendiente de dispersar
                                            </span>
                                            <span v-if="profit.status == STATUS.COMPLETED" class="badge bg-success">
                                                Dispersada
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr class="text-center">
                                        <td></td>
                                        <td>Total</td>
                                        <td><p class="fw-semibold">$ {{totals.amount.numberFormat(2)}}</p></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div v-else-if="profits == false">
                    <div class="alert alert-light text-center">
                        <div>No tenemos información sobre tus ganancias.</div>
                    </div>
                </div>
            </div>
        </div>
    `,
}

export { GainsViewer } 