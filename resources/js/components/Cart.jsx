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
            discount: 0,
            // For adding items to existing order
            activeOrderId: null,
            activeOrderInfo: null,
            // Category filtering
            activeCategory: "All",
            categories: [],
            // Variant Selection
            showVariantModal: false,
            selectedParent: null,
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
        this.handleAddItemsToOrder = this.handleAddItemsToOrder.bind(this);
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

        // Check URL params for adding items to existing order
        const urlParams = new URLSearchParams(window.location.search);
        const orderId = urlParams.get("order_id");
        if (orderId) {
            this.setState({
                activeOrderId: parseInt(orderId),
                activeOrderInfo: {
                    id: parseInt(orderId),
                    table_no: urlParams.get("table_no") || "",
                },
            });
        }
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

    loadProducts(search = "", category = "") {
        let query = !!search ? `?search=${search}` : "?";
        if (category && category !== "All") {
            query += (query === "?" ? "" : "&") + `category=${category}`;
        }
        
        axios.get(`/admin/products${query}`).then((res) => {
            const products = res.data.data;
            
            // Extract unique categories from products if needed, 
            // but better to have them pre-defined or fetched once
            if (this.state.categories.length === 0) {
                // For demo/fallback: Extract categories from existing products
                const productCats = products.map(p => p.category).filter(c => !!c);
                const uniqueCats = ["All", ...new Set(productCats)];
                this.setState({ categories: uniqueCats });
            }
            
            this.setState({ products });
        });
    }

    handleCategoryClick(category) {
        this.setState({ activeCategory: category });
        this.loadProducts(this.state.search, category);
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

    addProductToCart(product) {
        if (product.variants && product.variants.length > 0) {
            this.setState({ selectedParent: product, showVariantModal: true });
            return;
        }

        const barcode = product.barcode;
        let cartItem = this.state.cart.find((c) => c.id === product.id);
        if (!!cartItem) {
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
                const newProduct = {
                    ...product,
                    pivot: {
                        quantity: 1,
                        product_id: product.id,
                        user_id: 1,
                    },
                };

                this.setState({ cart: [...this.state.cart, newProduct] });
            }
        }

        axios
            .post("/admin/cart", { barcode })
            .catch((err) => {
                Swal.fire("Error!", err.response.data.message, "error");
            });
    }

    addVariantToCart(variant) {
        this.setState({ showVariantModal: false, selectedParent: null });
        this.addProductToCart(variant);
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

    /**
     * Place Order — creates order WITHOUT payment, prints order receipt
     */
    handleClickSubmit() {
        Swal.fire({
            title: "Place Order",
            text: `Total: ${window.APP.currency_symbol} ${this.getTotal(this.state.cart)}. Place this order?`,
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Place Order",
            cancelButtonText: "Cancel",
            confirmButtonColor: "#28a745",
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return axios
                    .post("/admin/orders", {
                        customer_id: this.state.customer_id,
                        amount: 0,
                        instruction: this.state.instruction,
                        type: this.state.order_type,
                        table_no: this.state.table_no,
                        discount: this.state.discount,
                    })
                    .then((res) => {
                        this.loadCart();
                        this.loadProducts();
                        return res.data;
                    })
                    .catch((err) => {
                        Swal.showValidationMessage(err.response.data.message);
                    });
            },
            allowOutsideClick: () => !Swal.isLoading(),
        }).then((result) => {
            if (result.value) {
                const orderId = result.value.order.id;

                Swal.fire({
                    title: "Order Placed!",
                    text: `Order #${orderId} has been placed successfully.`,
                    icon: "success",
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-print"></i> Print Order',
                    denyButtonText: '<i class="fas fa-utensils"></i> Send to Kitchen',
                    cancelButtonText: "Close",
                    confirmButtonColor: '#3085d6',
                    denyButtonColor: '#f39c12',
                }).then((printResult) => {
                    if (printResult.isConfirmed) {
                        const printWindow = window.open(
                            `/admin/orders/${orderId}/receipt`,
                            "_blank"
                        );
                        if (printWindow) printWindow.focus();
                    } else if (printResult.isDenied) {
                        const printWindow = window.open(
                            `/admin/orders/${orderId}/kitchen-receipt`,
                            "_blank"
                        );
                        if (printWindow) printWindow.focus();
                    }
                });

                // Reset form state
                this.setState({
                    instruction: "",
                    discount: 0,
                    table_no: "",
                    order_type: "Dine-in",
                });
            }
        });
    }

    /**
     * Add Items — append cart items to an existing open order
     */
    handleAddItemsToOrder() {
        const { activeOrderId, cart } = this.state;
        if (!activeOrderId || !cart.length) return;

        Swal.fire({
            title: "Add Items to Order",
            text: `Add ${cart.length} item(s) to Order #${activeOrderId}?`,
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Add Items",
            cancelButtonText: "Cancel",
            confirmButtonColor: "#007bff",
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return axios
                    .post(`/admin/orders/${activeOrderId}/add-items`)
                    .then((res) => {
                        this.loadCart();
                        this.loadProducts();
                        return res.data;
                    })
                    .catch((err) => {
                        Swal.showValidationMessage(
                            err.response?.data?.message || "An error occurred"
                        );
                    });
            },
            allowOutsideClick: () => !Swal.isLoading(),
        }).then((result) => {
            if (result.value) {
                Swal.fire({
                    title: "Items Added!",
                    text: `Items added to Order #${activeOrderId}.`,
                    icon: "success",
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-print"></i> Print Order',
                    denyButtonText: '<i class="fas fa-utensils"></i> Send to Kitchen',
                    cancelButtonText: "Close",
                    confirmButtonColor: '#3085d6',
                    denyButtonColor: '#f39c12',
                }).then((printResult) => {
                    if (printResult.isConfirmed) {
                        const printWindow = window.open(
                            `/admin/orders/${activeOrderId}/receipt`,
                            "_blank"
                        );
                        if (printWindow) printWindow.focus();
                    } else if (printResult.isDenied) {
                        const printWindow = window.open(
                            `/admin/orders/${activeOrderId}/kitchen-receipt`,
                            "_blank"
                        );
                        if (printWindow) printWindow.focus();
                    }
                    // Redirect back to orders
                    window.location.href = "/admin/orders";
                });
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
            activeOrderId,
            activeOrderInfo,
            showVariantModal,
            selectedParent,
        } = this.state;

        const isAddingItems = !!activeOrderId;

        return (
            <div className="row" style={{ position: 'relative' }}>
                {/* Variant Selection Modal */}
                {showVariantModal && (
                    <div style={{
                        position: 'fixed',
                        top: 0, left: 0, width: '100%', height: '100%',
                        backgroundColor: 'rgba(0,0,0,0.5)',
                        zIndex: 1000,
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center'
                    }}>
                        <div className="card" style={{ width: '400px', borderRadius: '15px', overflow: 'hidden' }}>
                            <div className="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 className="mb-0">Select Variant: {selectedParent?.name}</h5>
                                <button className="btn btn-sm text-white" onClick={() => this.setState({ showVariantModal: false })}>&times;</button>
                            </div>
                            <div className="card-body p-0">
                                <div className="list-group list-group-flush">
                                    {selectedParent?.variants.map(variant => (
                                        <button 
                                            key={variant.id}
                                            className="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                            onClick={() => this.addVariantToCart(variant)}
                                            style={{ padding: '15px 20px' }}
                                        >
                                            <div>
                                                <span style={{ fontWeight: '600' }}>{variant.variant_name || variant.name}</span>
                                                <small className="ml-2 text-muted">({variant.quantity} in stock)</small>
                                            </div>
                                            <span className="badge badge-primary badge-pill">
                                                {window.APP.currency_symbol} {variant.price}
                                            </span>
                                        </button>
                                    ))}
                                </div>
                            </div>
                            <div className="card-footer bg-light">
                                <button className="btn btn-secondary btn-block" onClick={() => this.setState({ showVariantModal: false })}>Cancel</button>
                            </div>
                        </div>
                    </div>
                )}

                <div className="col-md-6 col-lg-4">
                    {/* Active Order Banner */}
                    {isAddingItems && (
                        <div
                            className="alert alert-info d-flex align-items-center mb-3"
                            style={{
                                borderRadius: "10px",
                                border: "2px solid #17a2b8",
                                background: "linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%)",
                            }}
                        >
                            <i
                                className="fas fa-plus-circle mr-2"
                                style={{ fontSize: "1.3em" }}
                            ></i>
                            <div>
                                <strong>Adding items to Order #{activeOrderId}</strong>
                                {activeOrderInfo?.table_no && (
                                    <span className="ml-2 badge badge-secondary">
                                        Table {activeOrderInfo.table_no}
                                    </span>
                                )}
                            </div>
                        </div>
                    )}

                    <div className="row mb-2">
                        <div className="col">
                            <form onSubmit={this.handleScanBarcode}>
                                <input
                                    type="text"
                                    className="form-control"
                                    placeholder={translations["scan_barcode"]}
                                    value={barcode}
                                    onChange={this.handleOnChangeBarcode}
                                    style={{ borderRadius: "8px" }}
                                />
                            </form>
                        </div>
                        {!isAddingItems && (
                            <div className="col">
                                <select
                                    className="form-control"
                                    onChange={this.setTableNo}
                                    style={{ borderRadius: "8px" }}
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
                        )}
                    </div>

                    <div className="user-cart">
                        <div
                            className="card"
                            style={{
                                borderRadius: "12px",
                                boxShadow: "0 4px 15px rgba(0,0,0,0.08)",
                            }}
                        >
                            <table className="table table-striped mb-0">
                                <thead style={{ background: "#f8f9fa" }}>
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
                                            <td style={{ verticalAlign: 'middle' }}>
                                                <strong>{c.name}</strong>
                                                {c.variant_name && <small className="d-block text-muted">{c.variant_name}</small>}
                                            </td>
                                            <td style={{ verticalAlign: 'middle' }}>
                                                <div className="d-flex align-items-center" style={{ gap: '5px' }}>
                                                    <div className="input-group input-group-sm" style={{ width: '110px' }}>
                                                        <div className="input-group-prepend">
                                                            <button 
                                                                className="btn btn-outline-primary" 
                                                                type="button"
                                                                onClick={() => this.handleChangeQty(c.id, Math.max(1, parseInt(c.pivot.quantity) - 1))}
                                                                style={{ borderRadius: '6px 0 0 6px' }}
                                                            >
                                                                <i className="fas fa-minus"></i>
                                                            </button>
                                                        </div>
                                                        <input
                                                            type="text"
                                                            className="form-control text-center px-1"
                                                            value={c.pivot.quantity}
                                                            onChange={(event) =>
                                                                this.handleChangeQty(
                                                                    c.id,
                                                                    event.target.value
                                                                )
                                                            }
                                                            style={{ minWidth: '35px' }}
                                                        />
                                                        <div className="input-group-append">
                                                            <button 
                                                                className="btn btn-outline-primary" 
                                                                type="button"
                                                                onClick={() => this.handleChangeQty(c.id, parseInt(c.pivot.quantity) + 1)}
                                                                style={{ borderRadius: '0 6px 6px 0' }}
                                                            >
                                                                <i className="fas fa-plus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <button
                                                        className="btn btn-danger btn-sm"
                                                        onClick={() =>
                                                            this.handleClickDelete(c.id)
                                                        }
                                                        style={{ borderRadius: "6px", height: '31px' }}
                                                        title="Remove item"
                                                    >
                                                        <i className="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td className="text-right" style={{ verticalAlign: 'middle' }}>
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

                    {!isAddingItems && (
                        <>
                            <div className="form-group">
                                <textarea
                                    className="form-control"
                                    value={instruction}
                                    onChange={this.handleInstructionChange}
                                    placeholder={
                                        translations["enter_instruction"] ||
                                        "Enter any special instruction..."
                                    }
                                    style={{ borderRadius: "8px" }}
                                />
                            </div>
                            <div className="form-group">
                                <select
                                    className="form-control"
                                    value={order_type}
                                    onChange={this.handleOrderTypeChange}
                                    style={{ borderRadius: "8px" }}
                                >
                                    <option value="Dine-in">
                                        {translations["dine_in"] || "Dine-in"}
                                    </option>
                                    <option value="Delivery">
                                        {translations["delivery"] || "Delivery"}
                                    </option>
                                </select>
                            </div>

                            <div className="row mb-2">
                                <div className="col">
                                    <label>
                                        {translations["discount"] || "Discount"}:
                                    </label>
                                </div>
                                <div className="col">
                                    <input
                                        type="number"
                                        className="form-control"
                                        value={this.state.discount}
                                        onChange={(e) =>
                                            this.setState({
                                                discount:
                                                    parseFloat(e.target.value) || 0,
                                            })
                                        }
                                        placeholder="Enter discount amount"
                                        min="0"
                                        style={{ borderRadius: "8px" }}
                                    />
                                </div>
                            </div>
                        </>
                    )}

                    <div
                        className="row py-2 mb-2"
                        style={{
                            background: "linear-gradient(135deg, #667eea 0%, #764ba2 100%)",
                            borderRadius: "10px",
                            color: "#fff",
                            padding: "10px 15px",
                        }}
                    >
                        <div className="col" style={{ fontSize: "1.1em", fontWeight: "600" }}>
                            {translations["total"]}:
                        </div>
                        <div
                            className="col text-right"
                            style={{ fontSize: "1.3em", fontWeight: "700" }}
                        >
                            {window.APP.currency_symbol} {this.getTotal(cart)}
                        </div>
                    </div>

                    <div className="row mt-2">
                        <div className="col">
                            <button
                                type="button"
                                className="btn btn-danger btn-block"
                                onClick={this.handleEmptyCart}
                                disabled={!cart.length}
                                style={{
                                    borderRadius: "10px",
                                    padding: "10px",
                                    fontWeight: "600",
                                }}
                            >
                                <i className="fas fa-times mr-1"></i>
                                {translations["cancel"] || "Cancel"}
                            </button>
                        </div>
                        <div className="col">
                            {isAddingItems ? (
                                <button
                                    type="button"
                                    className="btn btn-info btn-block"
                                    disabled={!cart.length}
                                    onClick={this.handleAddItemsToOrder}
                                    style={{
                                        borderRadius: "10px",
                                        padding: "10px",
                                        fontWeight: "600",
                                    }}
                                >
                                    <i className="fas fa-plus mr-1"></i>
                                    Add to Order #{activeOrderId}
                                </button>
                            ) : (
                                <button
                                    type="button"
                                    className="btn btn-block"
                                    disabled={!cart.length}
                                    onClick={this.handleClickSubmit}
                                    style={{
                                        borderRadius: "10px",
                                        padding: "10px",
                                        fontWeight: "600",
                                        background:
                                            "linear-gradient(135deg, #28a745 0%, #20c997 100%)",
                                        color: "#fff",
                                        border: "none",
                                    }}
                                >
                                    <i className="fas fa-check mr-1"></i>
                                    {translations["place_order"] || "Place Order"}
                                </button>
                            )}
                        </div>
                    </div>
                </div>

                <div className="col-md-6 col-lg-8">
                    {/* Category Tabs */}
                    <div className="mb-3 d-flex overflow-auto pb-2" style={{ gap: '10px', scrollbarWidth: 'thin' }}>
                        {(this.state.categories.length > 0 ? this.state.categories : ["All"]).map(cat => (
                            <button
                                key={cat}
                                onClick={() => this.handleCategoryClick(cat)}
                                className={`btn btn-sm ${this.state.activeCategory === cat ? 'btn-primary' : 'btn-outline-secondary'}`}
                                style={{
                                    whiteSpace: 'nowrap',
                                    borderRadius: '20px',
                                    padding: '6px 15px',
                                    fontSize: '0.9em',
                                    fontWeight: '600',
                                    border: '1.5px solid'
                                }}
                            >
                                {cat}
                            </button>
                        ))}
                    </div>

                    <div className="mb-3">
                        <input
                            type="text"
                            className="form-control"
                            placeholder={
                                (translations["search_product"] || "Search Product") +
                                "..."
                            }
                            onChange={this.handleChangeSearch}
                            onKeyDown={this.handleSeach}
                            style={{
                                borderRadius: "10px",
                                padding: "10px 15px",
                                boxShadow: "0 2px 8px rgba(0,0,0,0.08)",
                                border: '1.5px solid #e2e8f0'
                            }}
                        />
                    </div>
                    
                    <div className="order-product-wrapper" style={{ maxHeight: '700px', overflowY: 'auto', padding: '5px' }}>
                        <div className="order-product" style={{ 
                            display: 'grid', 
                            gridTemplateColumns: 'repeat(auto-fill, minmax(160px, 1fr))', 
                            gap: '15px' 
                        }}>
                            {products.length > 0 ? products.map((p) => (
                                <div
                                    onClick={() => this.addProductToCart(p)}
                                    key={p.id}
                                    className="item"
                                    style={{
                                        borderRadius: "12px",
                                        overflow: "hidden",
                                        cursor: "pointer",
                                        transition: "all 0.25s ease",
                                        boxShadow: "0 4px 12px rgba(0,0,0,0.08)",
                                        background: '#fff',
                                        margin: '0',
                                        border: '1px solid #f0f0f0',
                                        position: 'relative',
                                        display: 'flex',
                                        flexDirection: 'column',
                                        height: '220px'
                                    }}
                                    onMouseEnter={(e) => {
                                        e.currentTarget.style.transform = 'translateY(-4px)';
                                        e.currentTarget.style.boxShadow = '0 8px 20px rgba(0,0,0,0.12)';
                                    }}
                                    onMouseLeave={(e) => {
                                        e.currentTarget.style.transform = 'translateY(0)';
                                        e.currentTarget.style.boxShadow = '0 4px 12px rgba(0,0,0,0.08)';
                                    }}
                                >
                                    <div style={{ height: '130px', width: '100%', overflow: 'hidden', background: '#f8f9fa' }}>
                                        <img 
                                            src={p.image_url} 
                                            alt={p.name} 
                                            style={{ width: '100%', height: '100%', objectFit: 'cover' }} 
                                        />
                                    </div>
                                    <div style={{ padding: '10px', flexGrow: 1, display: 'flex', flexDirection: 'column', justifyContent: 'space-between' }}>
                                        <h5 style={{ 
                                            fontSize: '0.9rem', 
                                            fontWeight: '700', 
                                            margin: '0 0 4px 0',
                                            color: '#2d3748',
                                            display: '-webkit-box',
                                            WebkitLineClamp: 2,
                                            WebkitBoxOrient: 'vertical',
                                            overflow: 'hidden',
                                            lineHeight: '1.3'
                                        }}>
                                            {p.name}
                                        </h5>
                                        <div className="d-flex justify-content-between align-items-center mt-auto">
                                            <span style={{ 
                                                fontSize: '0.95rem', 
                                                fontWeight: '800', 
                                                color: '#667eea' 
                                            }}>
                                                {window.APP.currency_symbol} {p.price}
                                            </span>
                                            <span className={`badge ${p.quantity <= window.APP.warning_quantity ? 'badge-danger' : 'badge-light'}`} style={{ fontSize: '0.75rem', padding: '4px 8px', borderRadius: '6px' }}>
                                                {p.quantity} left
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            )) : (
                                <div className="col-12 text-center py-5">
                                    <i className="fas fa-search fa-3x text-muted mb-3"></i>
                                    <p className="text-muted">No products found in this category.</p>
                                </div>
                            )}
                        </div>
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
