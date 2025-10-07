   <h1>Server Usage Reports</h1>

   <h2>RAM Usage Table</h2>
   <?php if (!empty($ram_data)) : ?>
       <table class="widefat fixed striped">
           <thead>
               <tr>
                   <th>RAM Usage (MB)</th>
                   <th>Recorded At</th>
               </tr>
           </thead>
           <tbody>
               <?php foreach ($ram_data as $row) : ?>
                   <tr>
                       <td><?php echo esc_html($row['value_usage']); ?></td>
                       <td><?php echo esc_html($row['created_at']); ?></td>
                   </tr>
               <?php endforeach; ?>
           </tbody>
       </table>
   <?php else : ?>
       <p>No RAM usage data found.</p>
   <?php endif; ?>
   <canvas id="ramChart" width="600" height="300"></canvas>

   <h2>CPU Usage</h2>
   <p>CPU Usage data is not stored in a table.</p>
   <?php if (!empty($cpu_data)) : ?>
       <table class="widefat fixed striped">
           <thead>
               <tr>

                   <th>CPU Usage (%)</th>
                   <th>Recorded At</th>


               </tr>
           </thead>
           <tbody>
               <?php foreach ($cpu_data as $row) : ?>
                   <tr>
                       <td><?php echo esc_html($row['value_usage']); ?></td>
                       <td><?php echo esc_html($row['created_at']); ?></td>
                   </tr>
               <?php endforeach; ?>
           </tbody>
       </table>
   <?php else : ?>
       <p>No CPU usage data found.</p>
   <?php endif; ?>