pipeline {
    agent { label 'PHPService' }
    
    stages {
		stage('Test') {
			steps {
				script {
					def existsDBConnection = fileExists 'dbConnection.php'
					def existsService = fileExists 'service.php'

					if (!existsDBConnection) {
						currentBuild.result = 'ABORTED'
						error('File dbConnection.php does not exists.')
					}
					
					if (!existsService) {
						currentBuild.result = 'ABORTED'
						error('File service.php does not exists.')
					}
				}
			}
		}
	
		stage('Deploy') {
            steps {
				sh 'sudo rm /var/www/html/service.php | true'
				sh 'sudo rm /var/www/html/dbConnection.php | true'
				
				sh 'sudo cp service.php /var/www/html'
				sh 'sudo cp dbConnection.php /var/www/html'
            }
        }
	}
}