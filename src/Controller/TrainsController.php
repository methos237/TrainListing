<?php

namespace Trains\Controller;

use ComponentSystem\Page;
use Http\Controller;
use Http\HttpRequest;
use Http\HttpResponse;
use Trains\Model\Train;
use Trains\Model\TrainsCSVParser;
use Trains\Model\TrainsDao;
use Trains\View\FileUploadForm;
use Trains\View\Footer;
use Trains\View\Header;
use Trains\View\TrainListing;
use Trains\View\TrainsPage;

class TrainsController extends Controller {
	
	private TrainsDao $dao;
	private ?string $errorMessage = null;
	
	public function __construct(HttpRequest $request = null) {
		parent::__construct($request);
		$this->dao = new TrainsDao();
	}
	
	
	public function handleRequest(): ?HttpResponse {
		$statusMessage = null;
		
		if (isset($_POST['submit'])) {
			switch ($_POST['submit']) {
				case "upload" :
					if (isset($_FILES['train_csv'])) {
						if ($_FILES['train_csv']['error'] === 4) {
							$this->errorMessage =  "You must select a file to upload";
						} else if ($_FILES['train_csv']['type'] !== 'text/csv') {
							$this->errorMessage = "The uploaded file must be a CSV. Please try again.";
						} else {
							$file = $_FILES['train_csv']['tmp_name'];
							if ($this->importCsv($file)) {
								$statusMessage = "Trains successfully added.";
							} else {
								$statusMessage = "There was an error in adding trains from the CSV.";
							}
						}
					}
					break;
				case "edit":
					$train = new Train(htmlentities($_POST['line']), htmlentities($_POST['route']), htmlentities($_POST['run_number']), htmlentities($_POST['operator_id']), htmlentities($_POST['id']));
					if ($this->dao->updateTrainInformation($train)) {
						$statusMessage = "Train " . htmlentities($_POST['run_number']) . " has been updated";
					} else {
						$statusMessage = "There was an error in updating Train " . htmlentities($_POST['run_number']) . ".";
					}
					break;
				case "delete":
					$train = new Train(htmlentities($_POST['line']),htmlentities( $_POST['route']), htmlentities($_POST['run_number']), htmlentities($_POST['operator_id']), htmlentities($_POST['id']));
					if ($this->dao->deleteTrainInformation($train)) {
						$statusMessage = "Train " . htmlentities($_POST['run_number']) . " has been removed";
					} else {
						$statusMessage = "There was an error in removing Train " . htmlentities($_POST['run_number']) . ".";
					}
					break;
				case "add":
					$train = new Train(htmlentities($_POST['line']), htmlentities($_POST['route']), htmlentities($_POST['run_number']), htmlentities($_POST['operator_id']));
					if ($this->dao->storeSingleTrainInformation($train)) {
						$statusMessage = "Train " . htmlentities($_POST['run_number']) . " has been added";
					} else {
						$statusMessage = "There was an error in adding Train " . htmlentities($_POST['run_number']) . ".";
					}
					break;
			}
			
		}
		
		$trains = $this->dao->getAllTrainInformation();
		
		
		return (new Page("Train Listing", "Lists train data from an uploaded CSV file", null, null, new TrainsPage(new FileUploadForm($this->errorMessage), new TrainListing($trains, $statusMessage))))
			->setHeader(new Header())
			->setFooter(new Footer())->toHttpResponse();
	}
	
	private function importCsv(string $file): bool {
		$parser = new TrainsCSVParser($file);
		if (!$this->dao->storeMultipleTrainInformation($parser->getTrainDataArray())) {
			$this->errorMessage = "Error inserting train data into the database. Ensure that all trains in CSV are not already present.";
			return false;
		}
		return true;
	}
	
}