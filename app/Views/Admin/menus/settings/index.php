<?php
/**
 * template: wp-content/plugins/wc-payment-link/app/Views/Admin/settings/index.php
 * @var \WCPaymentLink\Model\LinkModel[] $links
 */

 if ( ! defined( 'ABSPATH' ) ) exit;

?>

<div class="wrap wpl-wrap px-8">
    <div class="my-4 flex align-middle">
        <h1 id="table-desc" class="text-3xl"><?php echo esc_html__('Payment Links', 'wc-payment-link'); ?></h1>
        <button id="add-payment-link" class="ml-4 p-8 pt-1 pb-1 bg-blue-600 text-white rounded justify-center hover:bg-[#316beb] cursor-pointer shadow-md">
            <?php echo esc_html__('New payment link', 'wc-payment-link'); ?>
        </button>
    </div>
    <hr class="mb-5"/>
    <div class="mb-5 w-1/2">
        <label for="default-search" class="mb-2 text-sm font-medium text-gray-900 sr-only"><?php echo esc_html__('Search', 'wc-payment-link'); ?></label>
        <form id="wc-link-search-form" class="flex items-center justify-start" action="admin.php?page=wc-payment-link-links">
            <div class="relative">
                <input type="search"
                    name="search"
                    id="default-search"
                    class="block w-[180px] h-4 !p-4 !text-sm text-gray-900 !border-0 !shadow-sm rounded-lg bg-gray-50"
                    placeholder="<?php echo esc_attr__('Search for links', 'wc-payment-link'); ?>"
                    value="<?php echo esc_attr( isset($search) ? $search : '')?>"
                />
            </div>
            <input type="hidden" value="wc-payment-link-links" name="page"/>
            <button class="ml-2 px-4 py-[8px] text-blue-600 bg-white rounded justify-center shadow-sm border-0 border-gray-300 cursor-pointer hover:bg-gray-50">
                <svg class="w-4 h-4 text-blue-600" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                </svg>
            </button>
        </form>
    </div>
    <div  class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table aria-describedby="table-desc" class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3">
                    <div>
                        <a class="flex items-center cursor-pointer order-table" data-order="name">
                            <?php echo esc_html__('Link Name', 'wc-payment-link'); ?>
                            <i class="fa-solid fa-sort w-4 h-3 ml-1.5"></i>
                        </a>
                    </div>
                </th>
                <th scope="col" class="px-6 py-3">
                    <div>
                        <a class="flex items-center cursor-pointer order-table" data-order="expire_at">
                            <?php echo esc_html__('Expire at', 'wc-payment-link'); ?>
                            <i class="fa-solid fa-sort w-4 h-3 ml-1.5"></i>
                        </a>
                    </div>
                </th>
                <th scope="col" class="px-6 py-3">
                    <div>
                        <a class="flex items-center cursor-pointer order-table" data-order="token">
                            <?php echo esc_html__('Token', 'wc-payment-link'); ?>
                            <i class="fa-solid fa-sort w-4 h-3 ml-1.5"></i>
                        </a>
                    </div>
                </th>
                <th scope="col" class="px-6 py-3">
                    <div>
                        <a class="flex items-center cursor-pointer">
                            <?php echo esc_html__('Products', 'wc-payment-link'); ?>
                        </a>
                    </div>
                </th>
                <th scope="col" class="px-6 py-3">
                    <div>
                        <a class="flex items-center cursor-pointer">
                            <?php echo esc_html__('Cart Total', 'wc-payment-link'); ?>
                        </a>
                    </div>
                </th>
                <th scope="col" class="px-6 py-3">
                    <div>
                        <a class="flex items-center cursor-pointer">
                            <?php echo esc_html__('Actions', 'wc-payment-link'); ?>
                        </a>
                    </div>
                </th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($links as $link): ?>
                <tr class="bg-white hover:bg-gray-100 border-b border-b-gray-100">
                    <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                        <?php echo esc_html($link->getName()) ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php echo esc_html($link->getExpireAt()->format('d/m/Y - H:i')) ?>
                    </td>
                    <td class="px-6 py-4">
                        <a href="<?php echo esc_url($link->getLinkUrl()); ?>" target="_blank" class="text-blue-400"><?php echo esc_html($link->getToken()) ?></a>
                    </td>
                    <td class="px-6 py-4">
                        <?php echo esc_html(count($link->getProducts())) ?>
                    </td>
                    <td class="px-6 py-4">
                        <?php echo wc_price($link->getCartTotal()); ?>
                    </td>
                    <td class="px-6 py-4 flex gap-2">
                        <a class="copy-element font-medium text-blue-950 no-underline hover:text-blue-600 hover:cursor-pointer"data-copy="<?php echo esc_url($link->getLinkUrl()); ?>">
                            <i class="fa-solid fa-copy"></i>
                        </a>
                        <span>|</span>
                        <a class="open-link-form font-medium text-black no-underline hover:text-black-800 hover:cursor-pointer" data-link='<?php echo esc_attr(json_encode($link->getData())); ?>'>
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                        <span>|</span>
                        <form method="post">
                            <input type="hidden" name="action" value="remove"/>
                            <input type="hidden" value="wc-payment-link-settings" name="page"/>
                            <input type="hidden" name="link" value="<?php echo esc_attr($link->getId()); ?>"/>
                            <button class="font-medium text-red-800 no-underline hover:text-red-600 hover:cursor-pointer">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php if (isset($pagination['pages']) && $pagination['pages'] > 1) : ?>
        <div>
            <nav aria-label="Page navigation example" class="mt-4 shadow-md rounded-md float-right">
                <ul class="inline-flex -space-x-px text-base h-10">
                    <li>
                        <a data-page="<?php echo esc_attr($pagination['current'] - 1 ?: 1); ?>"
                           class="pagination ml-0 rounded-l-lg flex items-center justify-center hover:cursor-pointer px-[15px] h-10 leading-tight text-gray-500 bg-white border border-gray-200 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                            <?php echo esc_html__('Previous', 'wc-payment-link');?>
                        </a>
                    </li>
                    <?php for($i = 1; $i <= $pagination['pages']; $i++): ?>
                        <li>
                            <?php $backgroundColor = $pagination['current'] === $i ? 'hover:bg-gray-100' : 'bg-white'; ?>
                            <a data-page="<?php echo esc_attr($i) ?>"
                               class="<?php echo esc_attr($backgroundColor); ?> pagination flex items-center justify-center hover:cursor-pointer px-[15px] h-10 leading-tight text-gray-500 border border-gray-200 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
                            >
                                <?php echo esc_html($i) ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    <li>
                    <li>
                        <a data-page="<?php echo esc_attr(min( $pagination['current'] + 1, $pagination['pages'])) ?>"
                           class="pagination rounded-r-lg flex items-center justify-center hover:cursor-pointer px-[15px] h-10 leading-tight text-gray-500 bg-white border border-gray-200 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"
                        >
                            <?php echo esc_html__('Next', 'wc-payment-link');?>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/modal.php'; ?>
