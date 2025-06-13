import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { viteStaticCopy } from 'vite-plugin-static-copy';
import * as packages from './package.json';
import fsExtra from 'fs-extra'; // Import fs-extra as a default import
import { join } from 'path';

export default defineConfig({
    plugins: [
        laravel({
            input: [

                // Resources paths 
                'resources/sass/app.scss', 
                'resources/js/app.js',

                // Resources assets js file paths
                'resources/assets/js/add-products.js',
                'resources/assets/js/analytics-dashboard.js',
                'resources/assets/js/apexchart.js',
                'resources/assets/js/apexcharts-area.js',
                'resources/assets/js/apexcharts-bar.js',
                'resources/assets/js/apexcharts-boxplot.js',
                'resources/assets/js/apexcharts-bubble.js',
                'resources/assets/js/apexcharts-candlestick.js',
                'resources/assets/js/apexcharts-column.js',
                'resources/assets/js/apexcharts-heatmap.js',
                'resources/assets/js/apexcharts-line.js',
                'resources/assets/js/apexcharts-mixed.js',
                'resources/assets/js/apexcharts-pie.js',
                'resources/assets/js/apexcharts-polararea.js',
                'resources/assets/js/apexcharts-radar.js',
                'resources/assets/js/apexcharts-radialbar.js',
                'resources/assets/js/apexcharts-rangearea.js',
                'resources/assets/js/apexcharts-scatter.js',
                'resources/assets/js/apexcharts-timeline.js',
                'resources/assets/js/apexcharts-treemap.js',
                'resources/assets/js/authentication.js',
                'resources/assets/js/blog-create.js',
                'resources/assets/js/blog.js',
                'resources/assets/js/canada.js',
                'resources/assets/js/cards.js',
                'resources/assets/js/cart.js',
                'resources/assets/js/chartjs-charts.js',
                'resources/assets/js/checkout.js',
                'resources/assets/js/choices.js',
                'resources/assets/js/color-picker.js',
                'resources/assets/js/courses-dashboard.js',
                'resources/assets/js/create-invoice.js',
                'resources/assets/js/create-project.js',
                'resources/assets/js/crm-companies.js',
                'resources/assets/js/crm-contacts.js',
                'resources/assets/js/crm-dashboard.js',
                'resources/assets/js/crm-deals.js',
                'resources/assets/js/crm-leads.js',
                'resources/assets/js/crypto-buy_sell.js',
                'resources/assets/js/crypto-currency-exchange.js',
                'resources/assets/js/crypto-dashboard.js',
                'resources/assets/js/crypto-marketcap.js',
                'resources/assets/js/crypto-transactions-list.js',
                'resources/assets/js/custom-switcher.js',
                'resources/assets/js/datatable.js',
                'resources/assets/js/date-time_pickers.js',
                'resources/assets/js/defaultmenu.js',
                'resources/assets/js/draggable-cards.js',
                'resources/assets/js/echarts.js',
                'resources/assets/js/ecommerce-dashboard.js',
                'resources/assets/js/edit-products.js',
                'resources/assets/js/error.js',
                'resources/assets/js/file-manager.js',
                'resources/assets/js/fileupload.js',
                'resources/assets/js/form-validation.js',
                'resources/assets/js/fullcalendar.js',
                'resources/assets/js/gallery.js',
                'resources/assets/js/google-maps.js',
                'resources/assets/js/grid.js',
                'resources/assets/js/hrm-dashboard.js',
                'resources/assets/js/invoice-list.js',
                'resources/assets/js/italy.js',
                'resources/assets/js/job-candidate-details.js',
                'resources/assets/js/job-details.js',
                'resources/assets/js/job-search-candidate.js',
                'resources/assets/js/job-search.js',
                'resources/assets/js/jobs-dashboard.js',
                'resources/assets/js/jobs-simplebar.js',
                'resources/assets/js/jobs-post.js',
                'resources/assets/js/jsvectormap.js',
                'resources/assets/js/landing.js',
                'resources/assets/js/leaflet.js',
                'resources/assets/js/mail-settings.js',
                'resources/assets/js/mail.js',
                'resources/assets/js/masonry.js',
                'resources/assets/js/modal.js',
                'resources/assets/js/nft-create.js',
                'resources/assets/js/nft-dashboard.js',
                'resources/assets/js/nft-simplebar.js',
                'resources/assets/js/nouislider.js',
                'resources/assets/js/personal-dashboard',
                'resources/assets/js/prism-custom.js',
                'resources/assets/js/product-details.js',
                'resources/assets/js/product-list.js',
                'resources/assets/js/profile.js',
                'resources/assets/js/projects-dashboard.js',
                'resources/assets/js/quill-editor.js',
                'resources/assets/js/ratings.js',
                'resources/assets/js/russia.js',
                'resources/assets/js/sales-dashboard.js',
                'resources/assets/js/sales-simplebar.js',
                'resources/assets/js/select2.js',
                'resources/assets/js/scrollspy.js',
                'resources/assets/js/spain.js',
                'resources/assets/js/stocks-dashboard.js',
                'resources/assets/js/swiper.js',
                'resources/assets/js/switch.js',
                'resources/assets/js/tabulator.js',
                'resources/assets/js/task-kanban-board.js',
                'resources/assets/js/task-list.js',
                'resources/assets/js/team.js',
                'resources/assets/js/terms_conditions.js',
                'resources/assets/js/Toasts.js',
                'resources/assets/js/todolist.js',
                'resources/assets/js/tom-select.js',
                'resources/assets/js/us-merc-en.js',
                'resources/assets/js/widgets.js',
                'resources/assets/js/wishlist.js',
            ],
            refresh: true,
        }),

        viteStaticCopy({
            targets: [
                {
                    src: ([
                        'resources/assets/images/', 
                        'resources/assets/iconfonts/', 
                        'resources/assets/video/', 
                        
                        'resources/assets/js/apex-github-data.js', 
                        'resources/assets/js/apexcharts-candlestick-seriesdata.js',
                        'resources/assets/js/apexcharts-dayjs.js',
                        'resources/assets/js/apexcharts-irregulardata.js',
                        'resources/assets/js/apexcharts-stock-prices.js', 
                        'resources/assets/js/authentication-main.js', 
                        'resources/assets/js/chat.js', 
                        'resources/assets/js/coming-soon.js', 
                        'resources/assets/js/dataseries.js',
                        'resources/assets/js/main.js', 
                        'resources/assets/js/products.js',
                        'resources/assets/js/show-password.js', 
                        'resources/assets/js/sticky.js', 
                        'resources/assets/js/two-step-verification.js',
                        'resources/assets/js/under-maintenance.js',
                    ]),
                    dest: 'assets/'
                }
            ]
        }),

        {
            // Use a custom plugin for copying distribution files
            name: 'copy-dist-files',
            writeBundle: async () => {
                const destDir = 'public/build/assets/libs';  // Update the destination directory
      
              for (const dep of Object.keys(packages.dependencies)) {
                const srcPath = join('node_modules', dep, 'dist');
                const destPath = join(destDir, dep);
      
                // Check if the 'dist' directory exists for the dependency
                if (await fsExtra.pathExists(srcPath)) {
                  // Copy the distribution files (contents of 'dist') to the destination directory
                  await fsExtra.copy(srcPath, destPath, {
                    overwrite: true,
                    recursive: true,
                  });
      
                  // Remove the 'dist' directory from the destination
                  await fsExtra.remove(join(destPath, 'dist'));
                } else {
                  // If 'dist' folder doesn't exist, check if the package itself exists and copy it.
                  const packageSrcPath = join('node_modules', dep);
                  const packageDestPath = join(destDir, dep);
      
                  if (await fsExtra.pathExists(packageSrcPath)) {
                    await fsExtra.copy(packageSrcPath, packageDestPath, {
                      overwrite: true,
                      recursive: true,
                    });
                  }
                }
              }
            },
        },

        {
            name: 'blade',
            handleHotUpdate({ file, server }) {
                if (file.endsWith('.blade.php')) {
                    server.ws.send({
                        type: 'full-reload',
                        path: '*',
                    });
                }
            },
        }
    ],
    build: {
        chunkSizeWarningLimit: 1600,
        outDir: 'public/build',
        emptyOutDir: true,
    },
});
    