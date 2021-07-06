<?php

/**
 * Creates a JSON page based on the parameters
 * 
 * @author Dalton Gray
 * 
 */
class JSONpage
{
  private $page;
  private $recordset;

  /**
   * @param $pathArr - an array containing the route information
   * @param $recordset - 
   *
   */
  public function __construct($pathArr, $recordset)
  {
    $this->recordset = $recordset;
    $path = (empty($pathArr[1])) ? "api" : $pathArr[1];

    switch ($path) {
      case 'api':
        $this->page = $this->json_welcome();
        break;
      case 'authors':
        $this->page = $this->json_authors();
        break;
      case 'update-session-typeId':
        $this->page = $this->json_update_session_typeId();
        break;
      case 'update-session-name':
        $this->page = $this->json_update_session_name();
        break;
      case 'schedule':
        $this->page = $this->json_schedule();
        break;
      case 'content':
        $this->page = $this->json_content();
        break;
      case 'login':
        $this->page = $this->json_login();
        break;
      case 'sessions':
        $this->page = $this->json_session();
        break;
      default:
        $this->page = $this->json_error();
        break;
    }
  }

  private function sanitiseString($x)
  {
    return substr(trim(filter_var($x, FILTER_SANITIZE_STRING)), 0, 20);
  }

  private function sanitiseNum($x)
  {
    return filter_var($x, FILTER_VALIDATE_INT, array("options" => array("min_range" => 0, "max_range" => 100000)));
  }

  private function json_welcome()
  {
    $msg = array("message" => "welcome", "author" => "Dalton Gray");
    $msg['Available endpoints'] = array(1 => "/api/", 2 => "/api/authors", 3 => "/api/update-session-typeId", 4 => "/api/update-session-name", 5 => "/api/schedule", 6 => "/api/content", 7 => "/api/login", 8 => "/api/sessions");
    $msg['status'] = 200;

    return json_encode($msg);
  }

  private function json_error()
  {
    $msg = array("message" => "error");
    return json_encode($msg);
  }
  /**
   * json_update_session_typeId
   * 
   * @return HTTP status code
   * @return message relating to the http status code
   * @return list of session names (name) and their respective sessionId's in JSON format
   * 
   */
  private function json_session()
  {
    $msg = array("status" => "200", "message" => "OK");

    $query  = "SELECT sessionId, name FROM sessions";
    $params = [];

    if (isset($_REQUEST['page'])) {
      $query .= " ORDER BY sessionId";
      $query .= " LIMIT 10 ";
      $query .= " OFFSET ";
      $query .= 10 * ($this->sanitiseNum($_REQUEST['page']) - 1);
      $nextPage = BASEPATH . "api/sessions?page=" . $this->sanitiseNum($_REQUEST['page'] + 1);
      $previousPage = BASEPATH . "api/sessions?page=" . $this->sanitiseNum($_REQUEST['page'] - 1);
    }


    $res = json_decode($this->recordset->getJSONRecordSet($query, $params), true);

    $res['status'] = 200;
    $res['message'] = "ok";
    $res['next_page'] = $nextPage;
    $res['previous_page'] = $previousPage;
    return json_encode($res);
  }

  /**
   * json_update_session_typeId
   * 
   * @return HTTP status code
   * @return message relating to the http status code
   * 
   */
  private function json_update_session_typeId()
  {
    $input = json_decode(file_get_contents("php://input"));

    if (!$input) {
      return json_encode(array("status" => 400, "message" => "Invalid request"));
    }
    if (is_null($input->token)) {
      return json_encode(array("status" => 401, "message" => "Not authorised"));
    }

    if (is_null($input->typeId) || is_null($input->sessionId)) {
      return json_encode(array("status" => 400, "message" => "Invalid request"));
    }

    try {
      $jwtkey = JWTKEY;
      $decoded = \Firebase\JWT\JWT::decode($input->token, $jwtkey, array('HS256'));
    } catch (UnexpectedValueException $e) {
      return json_encode(array("status" => 401, "message" => $e->getMessage()));
    }

    $query  = "UPDATE sessions SET typeId = :typeId WHERE sessionId = :sessionId";
    $params = ["typeId" => $input->typeId, "sessionId" => $input->sessionId];
    $res = $this->recordset->getJSONRecordSet($query, $params);
    return json_encode(array("status" => 200, "message" => "ok"));
  }
  /**
   * json_update_session_name
   * 
   * @return HTTP status code
   * @return message relating to the http status code
   * 
   */
  private function json_update_session_name()
  {
    $input = json_decode(file_get_contents("php://input"));

    if (!$input) {
      return json_encode(array("status" => 400, "message" => "Invalid request"));
    }
    if (is_null($input->token)) {
      return json_encode(array("status" => 401, "message" => "Not authorised"));
    }

    if (is_null($input->name) || is_null($input->sessionId)) {
      return json_encode(array("status" => 400, "message" => "Invalid request"));
    }

    try {
      $jwtkey = JWTKEY;
      $decoded = \Firebase\JWT\JWT::decode($input->token, $jwtkey, array('HS256'));
    } catch (UnexpectedValueException $e) {
      return json_encode(array("status" => 401, "message" => $e->getMessage()));
    }

    $query  = "UPDATE sessions SET name = :name WHERE sessionId = :sessionId";
    $params = ["name" => $input->name, "sessionId" => $input->sessionId];
    $res = $this->recordset->getJSONRecordSet($query, $params);
    return json_encode(array("status" => 200, "message" => "ok"));
  }

  /**
   * json_login
   * 
   * @return HTTP status code
   * @return message relating to the http status code
   * 
   */
  private function json_login()
  {
    $msg = "Invalid request. Username and password required";
    $status = 400;
    $token = null;
    $input = json_decode(file_get_contents("php://input"));

    if ($input) {

      if (!is_null($input->email) && !is_null($input->password)) {
        $query  = "SELECT username, email, password, admin FROM users WHERE email LIKE :email";
        $params = ["email" => $input->email];
        $res = json_decode($this->recordset->getJSONRecordSet($query, $params), true);
        $password = ($res['count']) ? $res['data'][0]['password'] : null;

        if (password_verify($input->password, $res['data'][0]['password'])) {
          $msg = "User authorised. Welcome " . $res['data'][0]['username'] . " " . $res['data'][0]['lastname'];
          $status = 200;

          $token = array();
          $token['email'] = $input->email;
          $token['admin'] = $res['data'][0]['admin'];
          $token['username'] = $res['data'][0]['username'];
          $token['iat'] = time();
          $token['exp'] = time() + ((60 * 60) * 3);

          $jwtkey = JWTKEY;
          $token = \Firebase\JWT\JWT::encode($token, $jwtkey);
        } else {
          $msg = "username or password are invalid";
          $status = 401;
        }
      }
    }

    return json_encode(array("status" => $status, "message" => $msg, "token" => $token));
  }

  /**
   * json_authors
   * 
   * @return HTTP status code
   * @return message relating to the http status code
   * @return link to the next page (if the page parameter is in use)
   * @return link to the previous page (if the page parameter is in use)
   * @return list of authors(full names) and their respective authorId's in JSON format
   * 
   */
  private function json_authors()
  {
    $msg = array("status" => "200", "message" => "OK");

    $query  = "SELECT authorId, name FROM authors";
    $params = [];

    if (isset($_REQUEST['search'])) {
      $query .= " WHERE name LIKE :term";
      $term = $this->sanitiseString("%" . $_REQUEST['search'] . "%");
      $params = ["term" => $term];
    } else {
      if (isset($_REQUEST['authorId'])) {
        $query .= " WHERE authorId = :authorId";
        $term = $this->sanitiseNum($_REQUEST['authorId']);
        $params = ["authorId" => $term];
      }
    }

    if (isset($_REQUEST['page'])) {
      $query .= " ORDER BY authorId";
      $query .= " LIMIT 10 ";
      $query .= " OFFSET ";
      $query .= 10 * ($this->sanitiseNum($_REQUEST['page']) - 1);
      $nextPage = BASEPATH . "api/authors?page=" . $this->sanitiseNum($_REQUEST['page'] + 1);
      $previousPage = BASEPATH . "api/authors?page=" . $this->sanitiseNum($_REQUEST['page'] - 1);
    }

    $res = json_decode($this->recordset->getJSONRecordSet($query, $params), true);

    $res['status'] = 200;
    $res['message'] = "ok";
    $res['next_page'] = $nextPage;
    $res['previous_page'] = $previousPage;
    return json_encode($res);
  }

  /**
   * json_content
   * 
   * @return HTTP status code
   * @return message relating to the http status code
   * @return link to the next page (if the page parameter is in use)
   * @return link to the previous page (if the page parameter is in use)
   * @return authorId, title, and abstract in JSON format
   * 
   */
  private function json_content()
  {
    $query  = " SELECT content_authors.authorId, content.title, content.abstract
                FROM content
                JOIN content_authors
                ON content_authors.contentId = content.contentId
                ";
    $params = [];
    $where = " WHERE ";
    $doneWhere = FALSE;

    if (isset($_REQUEST['authorId'])) {
      $where .= " content_authors.authorId = :authorId ";
      $doneWhere = TRUE;
      $term = $this->sanitiseNum($_REQUEST['authorId']);
      $params["authorId"] = $term;
    }

    if (isset($_REQUEST['search'])) {
      $doneWhere ? $where .= " AND " : $doneWhere = TRUE;
      $where .= " title LIKE :search";
      $term = $this->sanitiseString("%" . $_REQUEST['search'] . "%");
      $params["search"] = $term;
    }

    $query .= $doneWhere ? $where : "";

    if (isset($_REQUEST['page'])) {
      $query .= " ORDER BY content_authors.authorId";
      $query .= " LIMIT 10 ";
      $query .= " OFFSET ";
      $query .= 10 * ($this->sanitiseNum($_REQUEST['page']) - 1);
      $nextPage = BASEPATH . "api/content?page=" . $this->sanitiseNum($_REQUEST['page'] + 1);
      $previousPage = BASEPATH . "api/content?page=" . $this->sanitiseNum($_REQUEST['page'] - 1);
    }

    $res = json_decode($this->recordset->getJSONRecordSet($query, $params), true);

    $res['status'] = 200;
    $res['message'] = "ok";
    $res['next_page'] = $nextPage;
    $res['previous_page'] = $previousPage;
    return json_encode($res);
  }



  /**
   * json_schedule
   * 
   * @return HTTP status code
   * @return message relating to the http status code
   * @return link to the next page (if the page parameter is in use)
   * @return link to the previous page (if the page parameter is in use)
   * @return information about the conference schedule and presentations in JSON format
   * 
   */
  private function json_schedule()
  {

    $msg = array("status" => "200", "message" => "OK");

    $query  = " SELECT
                authors.name as sessionChair, sessions.name, slots.dayString, sessions.name,
                rooms.name as room,
                session_types.name as sessionType, slots.startHour, slots.startMinute,
                slots.endHour, slots.endMinute
                FROM sessions
                LEFT JOIN authors ON sessions.chairId = authors.authorId
                INNER JOIN slots ON slots.slotId = sessions.slotId
                INNER JOIN rooms ON rooms.roomId = sessions.roomId
                INNER JOIN session_types ON sessions.typeId = session_types.typeId
              ";
    $params = [];


    if (isset($_REQUEST['page'])) {
      $query .= " LIMIT 10 ";
      $query .= " OFFSET ";
      $query .= 10 * ($this->sanitiseNum($_REQUEST['page']) - 1);
      $nextPage = BASEPATH . "api/schedule?page=" . $this->sanitiseNum($_REQUEST['page'] + 1);
      $previousPage = BASEPATH . "api/schedule?page=" . $this->sanitiseNum($_REQUEST['page'] - 1);
    }

    $res = json_decode($this->recordset->getJSONRecordSet($query, $params), true);

    $res['status'] = 200;
    $res['message'] = "ok";
    $res['next_page'] = $nextPage;
    $res['previous_page'] = $previousPage;
    return json_encode($res);
  }

  public function get_page()
  {
    return $this->page;
  }
}
