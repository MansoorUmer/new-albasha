// import React, { Component } from "react";
// import { createRoot } from "react-dom";
// import axios from "axios";
// import Swal from "sweetalert2";
// import { sum } from "lodash";
//
// class Cart extends Component {
//     constructor(props) {
//         super(props);
//         this.state = {
//             cart: [],
//             products: [],
//             customers: [],
//             barcode: "",
//             search: "",
//             customer_id: "",
//             translations: {},
//             instruction: "",
//             order_type: "Dine-in", // default value
//         };
//
//         this.loadCart = this.loadCart.bind(this);
//         this.handleOnChangeBarcode = this.handleOnChangeBarcode.bind(this);
//         this.handleScanBarcode = this.handleScanBarcode.bind(this);
//         this.handleChangeQty = this.handleChangeQty.bind(this);
//         this.handleEmptyCart = this.handleEmptyCart.bind(this);
//
//         this.loadProducts = this.loadProducts.bind(this);
//         this.handleChangeSearch = this.handleChangeSearch.bind(this);
//         this.handleSeach = this.handleSeach.bind(this);
//         this.setCustomerId = this.setCustomerId.bind(this);
//         this.handleClickSubmit = this.handleClickSubmit.bind(this);
//         this.loadTranslations = this.loadTranslations.bind(this);
//     }
//
//     componentDidMount() {
//         // load user cart
//         this.loadTranslations();
//         this.loadCart();
//         this.loadProducts();
//         this.loadCustomers();
//     }
//
//     // load the transaltions for the react component
//     loadTranslations() {
//         axios
//             .get("/admin/locale/cart")
//             .then((res) => {
//                 const translations = res.data;
//                 this.setState({ translations });
//             })
//             .catch((error) => {
//                 console.error("Error loading translations:", error);
//             });
//     }
//
//     loadCustomers() {
//         axios.get(`/admin/customers`).then((res) => {
//             const customers = res.data;
//             this.setState({ customers });
//         });
//     }
//
//     loadProducts(search = "") {
//         const query = !!search ? `?search=${search}` : "";
//         axios.get(`/admin/products${query}`).then((res) => {
//             const products = res.data.data;
//             this.setState({ products });
//         });
//     }
//
//     handleOnChangeBarcode(event) {
//         const barcode = event.target.value;
//         console.log(barcode);
//         this.setState({ barcode });
//     }
//
//     loadCart() {
//         axios.get("/admin/cart").then((res) => {
//             const cart = res.data;
//             this.setState({ cart });
//         });
//     }
//
//     handleScanBarcode(event) {
//         event.preventDefault();
//         const { barcode } = this.state;
//         if (!!barcode) {
//             axios
//                 .post("/admin/cart", { barcode })
//                 .then((res) => {
//                     this.loadCart();
//                     this.setState({ barcode: "" });
//                 })
//                 .catch((err) => {
//                     Swal.fire("Error!", err.response.data.message, "error");
//                 });
//         }
//     }
//     handleChangeQty(product_id, qty) {
//         const cart = this.state.cart.map((c) => {
//             if (c.id === product_id) {
//                 c.pivot.quantity = qty;
//             }
//             return c;
//         });
//
//         this.setState({ cart });
//         if (!qty) return;
//
//         axios
//             .post("/admin/cart/change-qty", { product_id, quantity: qty })
//             .then((res) => {})
//             .catch((err) => {
//                 Swal.fire("Error!", err.response.data.message, "error");
//             });
//     }
//
//     getTotal(cart) {
//         const total = cart.map((c) => c.pivot.quantity * c.price);
//         return sum(total).toFixed(2);
//     }
//     handleClickDelete(product_id) {
//         axios
//             .post("/admin/cart/delete", { product_id, _method: "DELETE" })
//             .then((res) => {
//                 const cart = this.state.cart.filter((c) => c.id !== product_id);
//                 this.setState({ cart });
//             });
//     }
//     handleEmptyCart() {
//         axios.post("/admin/cart/empty", { _method: "DELETE" }).then((res) => {
//             this.setState({ cart: [] });
//         });
//     }
//     handleChangeSearch(event) {
//         const search = event.target.value;
//         this.setState({ search });
//     }
//     handleSeach(event) {
//         if (event.keyCode === 13) {
//             this.loadProducts(event.target.value);
//         }
//     }
//
//     addProductToCart(barcode) {
//         let product = this.state.products.find((p) => p.barcode === barcode);
//         if (!!product) {
//             // if product is already in cart
//             let cart = this.state.cart.find((c) => c.id === product.id);
//             if (!!cart) {
//                 // update quantity
//                 this.setState({
//                     cart: this.state.cart.map((c) => {
//                         if (
//                             c.id === product.id &&
//                             product.quantity > c.pivot.quantity
//                         ) {
//                             c.pivot.quantity = c.pivot.quantity + 1;
//                         }
//                         return c;
//                     }),
//                 });
//             } else {
//                 if (product.quantity > 0) {
//                     product = {
//                         ...product,
//                         pivot: {
//                             quantity: 1,
//                             product_id: product.id,
//                             user_id: 1,
//                         },
//                     };
//
//                     this.setState({ cart: [...this.state.cart, product] });
//                 }
//             }
//
//             axios
//                 .post("/admin/cart", { barcode })
//                 .then((res) => {
//                     // this.loadCart();
//                     console.log(res);
//                 })
//                 .catch((err) => {
//                     Swal.fire("Error!", err.response.data.message, "error");
//                 });
//         }
//     }
//
//     setCustomerId(event) {
//         this.setState({ customer_id: event.target.value });
//     }
//     handleClickSubmit() {
//         Swal.fire({
//             title: this.state.translations["received_amount"],
//             input: "text",
//             inputValue: this.getTotal(this.state.cart),
//             cancelButtonText: this.state.translations["cancel_pay"],
//             showCancelButton: true,
//             confirmButtonText: this.state.translations["confirm_pay"],
//             showLoaderOnConfirm: true,
//             preConfirm: (amount) => {
//                 return axios
//                     .post("/admin/orders", {
//                         customer_id: this.state.customer_id,
//                         amount,
//                     })
//                     .then((res) => {
//                         this.loadCart();
//                         return res.data;
//                     })
//                     .catch((err) => {
//                         Swal.showValidationMessage(err.response.data.message);
//                     });
//             },
//             allowOutsideClick: () => !Swal.isLoading(),
//         }).then((result) => {
//             if (result.value) {
//                 //
//             }
//         });
//     }
//     render() {
//         const { cart, products, customers, barcode, translations } = this.state;
//         return (
//             <div className="row">
//                 <div className="col-md-6 col-lg-4">
//                     <div className="row mb-2">
//                         <div className="col">
//                             <form onSubmit={this.handleScanBarcode}>
//                                 <input
//                                     type="text"
//                                     className="form-control"
//                                     placeholder={translations["scan_barcode"]}
//                                     value={barcode}
//                                     onChange={this.handleOnChangeBarcode}
//                                 />
//                             </form>
//                         </div>
//                         <div className="col">
//                             <select
//                                 className="form-control"
//                                 onChange={this.setCustomerId}
//                             >
//                                 <option value="">
//                                     {translations["general_customer"]}
//                                 </option>
//                                 {customers.map((cus) => (
//                                     <option
//                                         key={cus.id}
//                                         value={cus.id}
//                                     >{`${cus.first_name} ${cus.last_name}`}</option>
//                                 ))}
//                             </select>
//                         </div>
//                     </div>
//                     <div className="user-cart">
//                         <div className="card">
//                             <table className="table table-striped">
//                                 <thead>
//                                     <tr>
//                                         <th>{translations["product_name"]}</th>
//                                         <th>{translations["quantity"]}</th>
//                                         <th className="text-right">
//                                             {translations["price"]}
//                                         </th>
//                                     </tr>
//                                 </thead>
//                                 <tbody>
//                                     {cart.map((c) => (
//                                         <tr key={c.id}>
//                                             <td>{c.name}</td>
//                                             <td>
//                                                 <input
//                                                     type="text"
//                                                     className="form-control form-control-sm qty"
//                                                     value={c.pivot.quantity}
//                                                     onChange={(event) =>
//                                                         this.handleChangeQty(
//                                                             c.id,
//                                                             event.target.value
//                                                         )
//                                                     }
//                                                 />
//                                                 <button
//                                                     className="btn btn-danger btn-sm"
//                                                     onClick={() =>
//                                                         this.handleClickDelete(
//                                                             c.id
//                                                         )
//                                                     }
//                                                 >
//                                                     <i className="fas fa-trash"></i>
//                                                 </button>
//                                             </td>
//                                             <td className="text-right">
//                                                 {window.APP.currency_symbol}{" "}
//                                                 {(
//                                                     c.price * c.pivot.quantity
//                                                 ).toFixed(2)}
//                                             </td>
//                                         </tr>
//                                     ))}
//                                 </tbody>
//                             </table>
//                         </div>
//                     </div>
//
//                     <div className="row">
//                         <div className="col">{translations["total"]}:</div>
//                         <div className="col text-right">
//                             {window.APP.currency_symbol} {this.getTotal(cart)}
//                         </div>
//                     </div>
//                     <div className="row">
//                         <div className="col">
//                             <button
//                                 type="button"
//                                 className="btn btn-danger btn-block"
//                                 onClick={this.handleEmptyCart}
//                                 disabled={!cart.length}
//                             >
//                                 {translations["cancel"]}
//                             </button>
//                         </div>
//                         <div className="col">
//                             <button
//                                 type="button"
//                                 className="btn btn-primary btn-block"
//                                 disabled={!cart.length}
//                                 onClick={this.handleClickSubmit}
//                             >
//                                 {translations["checkout"]}
//                             </button>
//                         </div>
//                     </div>
//                 </div>
//                 <div className="col-md-6 col-lg-8">
//                     <div className="mb-2">
//                         <input
//                             type="text"
//                             className="form-control"
//                             placeholder={translations["search_product"] + "..."}
//                             onChange={this.handleChangeSearch}
//                             onKeyDown={this.handleSeach}
//                         />
//                     </div>
//                     <div className="order-product">
//                         {products.map((p) => (
//                             <div
//                                 onClick={() => this.addProductToCart(p.barcode)}
//                                 key={p.id}
//                                 className="item"
//                             >
//                                 <img src={p.image_url} alt="" />
//                                 <h5
//                                     style={
//                                         window.APP.warning_quantity > p.quantity
//                                             ? { color: "red" }
//                                             : {}
//                                     }
//                                 >
//                                     {p.name}({p.quantity})
//                                 </h5>
//                             </div>
//                         ))}
//                     </div>
//                 </div>
//             </div>
//         );
//     }
// }
//
//
// export default Cart;
//
// const root = document.getElementById("cart");
// if (root) {
//     const rootInstance = createRoot(root);
//     rootInstance.render(<Cart />);
// }
import React, { Component } from "react";
import { createRoot } from "react-dom";
import axios from "axios";
import Swal from "sweetalert2";
import { sum } from "lodash";

class Cart extends Component {
    constructor(props) {
        super(props);
        this.state = {
            cart: [],
            products: [],
            customers: [],
            barcode: "",
            search: "",
            customer_id: "",
            instruction: "",
            order_type: "Dine-in",
            table_no: "",
            translations: {},
        };

        this.loadCart = this.loadCart.bind(this);
        this.handleOnChangeBarcode = this.handleOnChangeBarcode.bind(this);
        this.handleScanBarcode = this.handleScanBarcode.bind(this);
        this.handleChangeQty = this.handleChangeQty.bind(this);
        this.handleEmptyCart = this.handleEmptyCart.bind(this);

        this.loadProducts = this.loadProducts.bind(this);
        this.handleChangeSearch = this.handleChangeSearch.bind(this);
        this.handleSeach = this.handleSeach.bind(this);
        this.setCustomerId = this.setCustomerId.bind(this);
        this.handleClickSubmit = this.handleClickSubmit.bind(this);
        this.loadTranslations = this.loadTranslations.bind(this);
        this.handleInstructionChange = this.handleInstructionChange.bind(this);
        this.setTableNo = this.setTableNo.bind(this);
        this.handleOrderTypeChange = this.handleOrderTypeChange.bind(this);
    }

    componentDidMount() {
        this.loadTranslations();
        this.loadCart();
        this.loadProducts();
        this.loadCustomers();
    }

    loadTranslations() {
        axios
            .get("/admin/locale/cart")
            .then((res) => {
                const translations = res.data;
                this.setState({ translations });
            })
            .catch((error) => {
                console.error("Error loading translations:", error);
            });
    }

    loadCustomers() {
        axios.get(`/admin/customers`).then((res) => {
            const customers = res.data;
            this.setState({ customers });
        });
    }

    loadProducts(search = "") {
        const query = !!search ? `?search=${search}` : "";
        axios.get(`/admin/products${query}`).then((res) => {
            const products = res.data.data;
            this.setState({ products });
        });
    }

    handleOnChangeBarcode(event) {
        const barcode = event.target.value;
        this.setState({ barcode });
    }

    loadCart() {
        axios.get("/admin/cart").then((res) => {
            const cart = res.data;
            this.setState({ cart });
        });
    }

    handleScanBarcode(event) {
        event.preventDefault();
        const { barcode } = this.state;
        if (!!barcode) {
            axios
                .post("/admin/cart", { barcode })
                .then((res) => {
                    this.loadCart();
                    this.setState({ barcode: "" });
                })
                .catch((err) => {
                    Swal.fire("Error!", err.response.data.message, "error");
                });
        }
    }

    handleChangeQty(product_id, qty) {
        const cart = this.state.cart.map((c) => {
            if (c.id === product_id) {
                c.pivot.quantity = qty;
            }
            return c;
        });

        this.setState({ cart });
        if (!qty) return;

        axios
            .post("/admin/cart/change-qty", { product_id, quantity: qty })
            .catch((err) => {
                Swal.fire("Error!", err.response.data.message, "error");
            });
    }

    getTotal(cart) {
        const total = cart.map((c) => c.pivot.quantity * c.price);
        return sum(total).toFixed(2);
    }

    handleClickDelete(product_id) {
        axios
            .post("/admin/cart/delete", { product_id, _method: "DELETE" })
            .then(() => {
                const cart = this.state.cart.filter((c) => c.id !== product_id);
                this.setState({ cart });
            });
    }

    handleEmptyCart() {
        axios.post("/admin/cart/empty", { _method: "DELETE" }).then(() => {
            this.setState({ cart: [] });
        });
    }

    handleChangeSearch(event) {
        const search = event.target.value;
        this.setState({ search });
    }

    handleSeach(event) {
        if (event.keyCode === 13) {
            this.loadProducts(event.target.value);
        }
    }

    addProductToCart(barcode) {
        let product = this.state.products.find((p) => p.barcode === barcode);
        if (!!product) {
            let cart = this.state.cart.find((c) => c.id === product.id);
            if (!!cart) {
                this.setState({
                    cart: this.state.cart.map((c) => {
                        if (
                            c.id === product.id &&
                            product.quantity > c.pivot.quantity
                        ) {
                            c.pivot.quantity += 1;
                        }
                        return c;
                    }),
                });
            } else {
                if (product.quantity > 0) {
                    product = {
                        ...product,
                        pivot: {
                            quantity: 1,
                            product_id: product.id,
                            user_id: 1,
                        },
                    };

                    this.setState({ cart: [...this.state.cart, product] });
                }
            }

            axios
                .post("/admin/cart", { barcode })
                .catch((err) => {
                    Swal.fire("Error!", err.response.data.message, "error");
                });
        }
    }

    setTableNo(event) {
        this.setState({ table_no: event.target.value });
    }

    setCustomerId(event) {
        this.setState({ customer_id: event.target.value });
    }

    handleInstructionChange(event) {
        this.setState({ instruction: event.target.value });
    }

    handleOrderTypeChange(event) {
        this.setState({ order_type: event.target.value });
    }

    // handleClickSubmit() {
    //     Swal.fire({
    //         title: this.state.translations["received_amount"],
    //         input: "text",
    //         inputValue: this.getTotal(this.state.cart),
    //         cancelButtonText: this.state.translations["cancel_pay"],
    //         showCancelButton: true,
    //         confirmButtonText: this.state.translations["confirm_pay"],
    //         showLoaderOnConfirm: true,
    //         preConfirm: (amount) => {
    //             return axios
    //                 .post("/admin/orders", {
    //                     customer_id: this.state.customer_id,
    //                     amount,
    //                     instruction: this.state.instruction,
    //                     type: this.state.order_type,
    //                     table_no: this.state.table_no,
    //                 })
    //                 .then((res) => {
    //                     this.loadCart();
    //                     return res.data;
    //                 })
    //                 .catch((err) => {
    //                     Swal.showValidationMessage(err.response.data.message);
    //                 });
    //         },
    //         allowOutsideClick: () => !Swal.isLoading(),
    //     }).then((result) => {
    //         if (result.value) {
    //             // You can show a success message or redirect
    //         }
    //     });
    // }
    handleClickSubmit() {
        Swal.fire({
            title: this.state.translations["received_amount"],
            input: "text",
            inputValue: this.getTotal(this.state.cart),
            cancelButtonText: this.state.translations["cancel_pay"],
            showCancelButton: true,
            confirmButtonText: this.state.translations["confirm_pay"],
            showLoaderOnConfirm: true,
            preConfirm: (amount) => {
                return axios
                    .post("/admin/orders", {
                        customer_id: this.state.customer_id,
                        amount,
                        instruction: this.state.instruction,
                        type: this.state.order_type,
                        table_no: this.state.table_no,
                    })
                    .then((res) => {
                        this.loadCart();
                        return res.data;
                    })
                    .catch((err) => {
                        Swal.showValidationMessage(err.response.data.message);
                    });
            },
            allowOutsideClick: () => !Swal.isLoading(),
        }).then((result) => {
            if (result.value) {
                console.log("result:",result);
                const orderId = result.value.order.id;
                // ✅ Open receipt in new tab for printing
                const printWindow = window.open(`/admin/orders/${orderId}/receipt`, "_blank");
                printWindow.focus();
            }
        });
    }

    render() {
        const {
            cart,
            products,
            customers,
            barcode,
            instruction,
            order_type,
            translations,
        } = this.state;

        return (
            <div className="row">
                <div className="col-md-6 col-lg-4">
                    <div className="row mb-2">
                        <div className="col">
                            <form onSubmit={this.handleScanBarcode}>
                                <input
                                    type="text"
                                    className="form-control"
                                    placeholder={translations["scan_barcode"]}
                                    value={barcode}
                                    onChange={this.handleOnChangeBarcode}
                                />
                            </form>
                        </div>
                        <div className="col">
                            <select
                                className="form-control"
                                onChange={this.setTableNo}
                            >
                                <option value="">Select Table</option>
                                <option value="1">Table 01</option>
                                <option value="2">Table 02</option>
                                <option value="3">Table 03</option>
                                <option value="4">Table 04</option>
                                <option value="5">Table 05</option>
                                <option value="6">Table 06</option>
                                <option value="7">Table 07</option>
                                <option value="8">Table 08</option>
                                <option value="9">Table 09</option>
                                <option value="10">Table 10</option>

                            </select>
                        </div>
                    </div>



                    <div className="user-cart">
                        <div className="card">
                            <table className="table table-striped">
                                <thead>
                                <tr>
                                    <th>{translations["product_name"]}</th>
                                    <th>{translations["quantity"]}</th>
                                    <th className="text-right">
                                        {translations["price"]}
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                {cart.map((c) => (
                                    <tr key={c.id}>
                                        <td>{c.name}</td>
                                        <td>
                                            <input
                                                type="text"
                                                className="form-control form-control-sm qty"
                                                value={c.pivot.quantity}
                                                onChange={(event) =>
                                                    this.handleChangeQty(
                                                        c.id,
                                                        event.target.value
                                                    )
                                                }
                                            />
                                            <button
                                                className="btn btn-danger btn-sm"
                                                onClick={() =>
                                                    this.handleClickDelete(
                                                        c.id
                                                    )
                                                }
                                            >
                                                <i className="fas fa-trash"></i>
                                            </button>
                                        </td>
                                        <td className="text-right">
                                            {window.APP.currency_symbol}{" "}
                                            {(
                                                c.price * c.pivot.quantity
                                            ).toFixed(2)}
                                        </td>
                                    </tr>
                                ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div className="form-group">
                        <textarea
                            className="form-control"
                            value={instruction}
                            onChange={this.handleInstructionChange}
                            placeholder={
                                translations["enter_instruction"] ||
                                "Enter any special instruction..."
                            }
                        />
                    </div>
                    <div className="form-group">
                        <select
                            className="form-control"
                            value={order_type}
                            onChange={this.handleOrderTypeChange}
                        >
                            <option value="Dine-in">
                                {translations["dine_in"] || "Dine-in"}
                            </option>
                            <option value="Delivery">
                                {translations["delivery"] || "Delivery"}
                            </option>
                        </select>
                    </div>

                    <div className="row">
                        <div className="col">{translations["total"]}:</div>
                        <div className="col text-right">
                            {window.APP.currency_symbol} {this.getTotal(cart)}
                        </div>
                    </div>
                    <div className="row">
                        <div className="col">
                            <button
                                type="button"
                                className="btn btn-danger btn-block"
                                onClick={this.handleEmptyCart}
                                disabled={!cart.length}
                            >
                                {translations["cancel"]}
                            </button>
                        </div>
                        <div className="col">
                            <button
                                type="button"
                                className="btn btn-primary btn-block"
                                disabled={!cart.length}
                                onClick={this.handleClickSubmit}
                            >
                                {translations["checkout"]}
                            </button>
                        </div>
                    </div>
                </div>

                <div className="col-md-6 col-lg-8">
                    <div className="mb-2">
                        <input
                            type="text"
                            className="form-control"
                            placeholder={translations["search_product"] + "..."}
                            onChange={this.handleChangeSearch}
                            onKeyDown={this.handleSeach}
                        />
                    </div>
                    <div className="order-product">
                        {products.map((p) => (
                            <div
                                onClick={() => this.addProductToCart(p.barcode)}
                                key={p.id}
                                className="item"
                            >
                                <img src={p.image_url} alt="" />
                                <h5
                                    style={
                                        window.APP.warning_quantity > p.quantity
                                            ? { color: "red" }
                                            : {}
                                    }
                                >
                                    {p.name}({p.quantity})
                                </h5>
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        );
    }
}

export default Cart;

const root = document.getElementById("cart");
if (root) {
    const rootInstance = createRoot(root);
    rootInstance.render(<Cart />);
}
