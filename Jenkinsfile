pipeline {
    agent { label 'PHPService' }
    
    stages {
		stage('Test') {
			steps {
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
	
		stage('Deploy') {
            steps {
				sh 'yes | cp -rf /home/jenkins-slave-01/db-service/* /var/www/html/'
            }
        }
	}
}