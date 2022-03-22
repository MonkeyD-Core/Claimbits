<?php
namespace Php;

class FileWrite 
{ 

    // Function to write the database config file
    function databaseConfig($path = null, $data = []) 
    {

        //create ../database/Database.php file  
        $file_info   = pathinfo($path['output_path']);
        $output_dir  = $file_info['dirname'];
        $output_file = $file_info['basename'];
        file_put_contents($output_dir.'/'.$output_file, null);

        if (file_exists($path['template_path'])) {
            // Open the file
            $database_file = file_get_contents($path['template_path']);

            //set new database configuration
            $new  = str_replace("{HOSTNAME}",$data['hostname'],$database_file);
            $new  = str_replace("{USERNAME}",$data['username'],$new);
            $new  = str_replace("{PASSWORD}",$data['password'],$new);
            $new  = str_replace("{DATABASE}",$data['database'],$new);

            // Write the new database.php file
            $handle = fopen($path['output_path'],'w+');

            // Chmod the file, in case the user forgot
            @chmod($path['output_path'],0777);

            // Verify file permissions
            if (is_writable($path['output_path'])) {
                // Write the file
                if (fwrite($handle,$new)) {
                    return true;
                } else {
                //file not write
                    return false;
                }
            } else {
                //file is not writeable
                return false;
            }
        } else {
            //file is not exists
            return false;
        }
        
    }

    //create .env file
    public function createEnvFile(){
        //check flag directory is exists
        if (is_dir('flag/')) {
            //create a env file in flag directory
            file_put_contents('flag/env', date('d-m-Y h:i:s a'));
        }
    }

}
