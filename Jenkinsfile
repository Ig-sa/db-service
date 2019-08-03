pipeline {
    agent { label 'PHPService' }
    
    stages {

		stage('Copy new version') {
            steps {
                sh 'ls  /var/www/html/'
                sh 'ls  /home/jenkins-slave-01/workspace/db-service'
				//  sh 'yes | cp -rf /home/jenkins-slave-01/db-service/* /var/www/html/'
            }
        }
	}
}