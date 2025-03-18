import {v4 as uuidv4} from 'uuid';
import Table from './table.js';
class Settings
{
    constructor() {
        this.addPaymentLink();
        this.editPaymentLink();
        this.copyLink();
        this.handlerToken();
        this.handleModalProducts();

        new Table();
    }

    handlerToken() {
        const button = document.querySelector('#generate-token');

        if (button) {
            button.addEventListener('click', () => {
                this.generateToken();
            });
        }
    }

    generateToken() {
        const token = document.querySelector('#token');

        if (token) {
            token.value = uuidv4();
        }
    }

    copyLink() {
        const elements = document.querySelectorAll('.copy-element');

        elements.forEach((element) => {
            element.addEventListener('click', () => {
                const text = element.getAttribute('data-copy');

                if (navigator?.clipboard?.writeText) {
                    navigator.clipboard.writeText(text);
                }
            })
        });
    }

    addPaymentLink() {
        const button = document.querySelector('#add-payment-link');
        button.addEventListener('click', () => {
            this.openModal();
            this.generateToken();
        });
    }

    editPaymentLink() {
        const anchors = document.querySelectorAll('.open-link-form');
        const close = document.querySelector('#close-link-form');

        anchors.forEach((anchor) => {
            anchor.addEventListener('click', () => {
                this.openModal();
                this.fillModalFields(anchor.getAttribute('data-link'));
            });
        });

        close.addEventListener('click', () => {
            this.closeModal();
        });
    }

    openModal() {
        const modal = document.querySelector('#link-form');

        this.clearModal(modal);
        this.clearModalProducts();

        modal.classList.remove('hidden');
    }

    closeModal() {
        const modal = document.querySelector('#link-form');

        this.clearModal(modal);
        this.clearModalProducts();
        modal.classList.add('hidden');
    }

    clearModal(modal) {
        if (!modal) return;

        const fields = [
            'name',
            'token',
            'expire_at',
            'expire_hour',
            'coupon',
            'modal-link-title',
            'hidden_link_id',
            'link_url'
        ];

        fields.forEach((field) => {
            const element = modal.querySelector(`#${field}`);

            switch (field) {
                case 'modal-link-title':
                    element.classList.add('hidden');
                    break;
                case 'link_url':
                    element.removeAttribute('href');
                    element.classList.add('hidden')
                    break;
                default:
                    if (element) element.value = '';
                    break;
            }
        });

        ['product-checkbox','product-number'].forEach((field) => {
            const elements = modal.querySelectorAll(`.${field}`);
            elements.forEach((element) => {
                element.value = '';

                if (field === 'product-checkbox') {
                    element.checked = false;
                }
            });
        })
    }

    fillModalFields(fields) {
        const object = JSON.parse(fields);
        if (object) {
            for (const key in object) {
                const element = document.querySelector(`#${key}`);

                switch (key) {
                    case 'products':
                        this.fillModalProducts(object[key]);
                    break;
                    case 'link_id':
                        element.innerText = `#${object[key]}`;
                        element.parentElement.parentElement.classList.remove('hidden');
                        document.querySelector(`#hidden_${key}`).value = object[key];
                    break;
                    case 'link_url':
                        element.setAttribute('href', object[key]);
                        element.classList.remove('hidden')
                    break;
                    default:
                        if (element) element.value = object[key];
                    break;

                }
            }
        }
    }

    fillModalProducts(products) {
        const productList = document.querySelector('#product-list');

        if (productList) {
            productList.value = JSON.stringify(products);
        }

        products.forEach(product => {
            const productsElements = document.querySelectorAll('.modal-product');
            productsElements.forEach(element => {
                const checkbox = element.querySelector('.product-checkbox');
                const number = element.querySelector('.product-number');

                if (parseInt(checkbox.getAttribute('data-id')) === product.product) {
                    checkbox.checked = true;
                    number.value = product.quantity
                }
            });
        });
    }

    clearModalProducts() {
        const productList = document.querySelector('#product-list');
        if (productList) {
            productList.value = '';
        }
    }

    handleModalProducts() {

        const checkboxes = document.querySelectorAll('.product-checkbox');
        const numbers = document.querySelectorAll('.product-number');

        [checkboxes, numbers].forEach((elements) => {
            elements.forEach((element) => {
                element.addEventListener('change', () => {
                    this.updateModalProducts();
                });
            });
        })

    }

    updateModalProducts() {
        const products = document.querySelectorAll('.modal-product');
        const list = [];

        products.forEach((product) => {
            const checkbox = product.querySelector('.product-checkbox');
            const number = product.querySelector('.product-number');

            if (checkbox?.checked) {
                if (!number.value) number.value = 1;

                list.push({
                    product: checkbox.getAttribute('data-id'),
                    quantity: number.value ? number.value : 0
                });

            } else {
                number.value = '';
            }

            checkbox.addEventListener("change", () => {
                if (!checkbox.checked) {
                    number.value = '';
                }
            });
        })

        const productList = document.querySelector('#product-list');
        if (productList) {
            productList.value = JSON.stringify(list);
        }

    }

}

document.addEventListener('DOMContentLoaded', () => {
    new Settings();
});
