<?php
namespace MiMFa\Library;
/**
 * A powerful library to connect and reflect everything for scripts
 *@copyright All rights are reserved for MiMFa Development Group
 *@author Mohammad Fathi
 *@see https://aseqbase.ir, https://github.com/aseqbase/aseqbase
 *@link https://github.com/aseqbase/aseqbase/wiki/Libraries#reflect See the Library Documentation
*/
class Reflect{

    public static function CommentParameters(string|null $comment){
        if(is_null($comment)) return [];
        $matches = preg_find_all("/@\w+\s*.*/", $comment);
        $res = [];
        $res["abstract"] = ltrim(Convert::ToString(preg_find_all("/^\s*\*?(?<=\s)\s*[^@][\s\w].*/mi", $comment)), "* \t\r\n\f\v");
        foreach ($matches as $value)
            $res[preg_find("/(?<=\@)\w+/", $value)] = preg_find("/\@\w+(?<=\s).*/", $value);
        return $res;
    }

    /**
     * To get each Features of a Class
     * @param mixed $object
     */
    public static function Get($objectOrReflection){
        if($objectOrReflection === null) return new Reflected();
        if($objectOrReflection instanceof Reflected) return $objectOrReflection;
        if($objectOrReflection instanceof \ReflectionClass) return new Reflected($objectOrReflection);
        return new Reflected(new \ReflectionClass($objectOrReflection), $objectOrReflection);
    }
    /**
     * To set each Features of a Class
     * @param mixed $object
     */
    public static function Set($objectOrReflection, array $newData = []){
        if($objectOrReflection === null) return null;
        if($objectOrReflection instanceof Reflected || $objectOrReflection instanceof \ReflectionClass){
            $reflection = self::Get($objectOrReflection);
            foreach ($newData as $key=>$value)
                if(isset($reflection[$key])) $reflection[$key]->Value = $value;
            return $reflection;
        }
        foreach ($newData as $key=>$value)
            if(isset($objectOrReflection->$key)) $objectOrReflection->$key = $value;
        return $objectOrReflection;
    }
    /**
     * To get fill Features by an array
     * @param mixed $object
     */
    public static function Fill($objectOrReflection, array $newValues = []){
        $objectOrReflection = self::Get($objectOrReflection);
        foreach ($newValues as $key=>$value)
            if(isset($objectOrReflection[$key])) $objectOrReflection[$key]->Value = $value??$objectOrReflection[$key]->DefaultValue;
        return $objectOrReflection;
    }
    /**
     * To get each Features of a Class
     * @param mixed $object
     */
    public static function Write($objectOrReflection, $path = null){
        $objectOrReflection = self::Get($objectOrReflection);
        $path = $path??$objectOrReflection->Path;
        $content = file_get_contents($path);
        foreach ($objectOrReflection as $name => $prop) {
            $start = preg_find("/(\s*class[\s\b]+\w+[\s\b]*[\w\W]*\{[\w\W]+\s+)(?=\$$name\W)/", $content);
            if($start) $content = $start.preg_replace("/^\$${$name}[^;]*(?:(?:(\"|')[\W\w]*\1[^;]*)|(?:[^;\"']*))*;/U",
                    "\$$name = ".Convert::ToValue($prop->Value,$prop->Vars),
                    substr($content, strlen($start)));
            else {
                $start = preg_find("/(\s*class[\s\b]+\w+[\s\b]*[\w\W]*\{\s*/", $content);
                if($start){
                    $indention = preg_find("/\s*$/",$start);
                    $content = $start.(
                        (is_null($prop->Comment)?null:preg_replace("/\r?\n\r?/", PHP_EOL.$indention, $prop->Comment).PHP_EOL.$indention).
                        (count($prop->Modifires)<1?null:implode(" ", $prop->Modifires)." ").
                        (is_null($prop->Name)?null:"\$$prop->Name").
                        (is_null($prop->Value)?";":" = ".Convert::ToValue($prop->Value).";").PHP_EOL.$indention
                    ).substr($content, strlen($start));
                }else throw new \Exception("There is not any class named '$name'!");
            }
        }
        return file_put_contents($path, $content);
    }
    /**
     * To get the Path of a Class
     * @param mixed $object
     */
    public static function GetPath($objectOrReflection){
        if($objectOrReflection === null) return null;
        if($objectOrReflection instanceof Reflected) return $objectOrReflection->Path;
        if($objectOrReflection instanceof \ReflectionClass) return (new Reflected($objectOrReflection))->Path;
        return (new Reflected(new \ReflectionClass($objectOrReflection)))->Path;
    }

    /**
     * To get all Features of a Class as a HTML Form
     * @param mixed $object
     */
    public static function GetForm($objectOrReflection):\MiMFa\Module\Form{
        MODULE("Form");
        $form = new \MiMFa\Module\Form();
        $form->Title = "Edit";
        $form->Id = "MainEditForm";
        $form->Image = "edit";
        $form->Template = "both";
        $form->Method = "POST";
        $form->Timeout = 60000;
        $form->SubmitLabel = "Update";
        $form->ResetLabel = "Reset";
        $form->Children = self::GetFields($objectOrReflection);
        return $form;
    }
    /**
     * To get each of Features of a Class as a form HTML Field
     * @param mixed $object
     */
    public static function GetFields($objectOrReflection){
        MODULE("Field");
        foreach (self::Get($objectOrReflection) as $key=>$value)
            yield new \MiMFa\Module\Field(key:$key, value:$value->Value, title:$value->Title, description:$value->Description, type:$value->Var[0]);
    }
    /**
     * To handle all Features received of a Class HTML Form
     * @param mixed $object
     */
    public static function HandleForm($objectOrReflection, array $newValues = []){
        $objectOrReflection = self::Fill($objectOrReflection, $newValues);
        return self::Write($objectOrReflection);
    }
}

class Reflected extends \ArrayObject{
    public string|null $Type = null;
    /**
     * The default name of object
     * @var string|null
     */
    public string|null $Name = null;
    /**
     * If used @title: to specify a readable title for UI views
     * @var string|null
     */
    public string|null $Title = null;
    /**
     * If used @description: to specify a readable description for UI views
     * @var string|null
     */
    public string|null $Description = null;
    /**
     * {bool, int, float, string, array<datatype>, etc.}: to indicate the variable or constant type. other useful type can be:
	enum-string: to indicate the legal string name for a variable
	class-string: to indicate the exist class name
	interface-string: to indicate the exist interface name
	lowercase-string, non-empty-string, non-empty-lowercase-string: to indicate a non empty string, lowercased or both at once
     * @var string
     */
    public string $Var = "mixed";
    /**
     * {bool, int, float, string, array<datatype>, etc.}: to indicate the variable or constant type. other useful type can be:
	enum-string: to indicate the legal string name for a variable
	class-string: to indicate the exist class name
	interface-string: to indicate the exist interface name
	lowercase-string, non-empty-string, non-empty-lowercase-string: to indicate a non empty string, lowercased or both at once
     * @var string
     */
    public array $Vars = ["mixed"];
    /**
     * If used @small, @medium, @large: to indicate the size of input box
     * @var float
     */
    public float $Size = 0.1;
    /**
     * If used @category categoryname: to specify a category to organize the documented element's package into
     * @var string|null
     */
    public string|null $Category = null;
    /**
     * If used @internal: to indicate the property should not be visible in the front-end it will be false, otherwise will be true
     * @var bool
     */
    public bool $Visible = true;
    /**
     * If used @access {public, private, protected}: to indicate access control documentation for an element, for example @access private prevents documentation of the following element (if enabled)
     * @var string|null
     */
    public string|null $Access = null;
    /**
     * If used @version versionstring [unspecified format]: to indicate the version of any element, including a page-level block
     * @var string|null
     */
    public string|null $Version = null;
    /**
     * If used @example /path/to/example.php [description]: to include an external example file with syntax highlighting
     * @var string|null
     */
    public string|null $Example = null;
    /**
     * If used @link URL [linktext]: to display a hyperlink to a URL in the documentation
     * @var string|null
     */
    public string|null $Link = null;
    /**
     * If used @see {file.ext, elementname, class::methodname(), class::$variablename, functionname(), function functionname}: to display a link to the documentation for an element, there can be unlimited number of values separated by commas
     * @var string|null
     */
    public string|null $See = null;
    /**
     * If used @author authorname: to indicate the author name of everythings. By default the authorname of everything are  Mohammad Fathi
     * @var string|null
     */
    public string|null $Author = null;
    /**
     * If used @author authorname: to indicate the author name of everythings. By default the authorname of everything are  Mohammad Fathi
     * @var string|null
     */
    public array $Authors = [];
    /**
     * If used @copyright copyright [information]: to document the copyright information of any element that can be documented. The default copyrights of everything are  for MiMFa Development Group
     * @var string|null
     */
    public string|null $Copyright = null;
    /**
     * If used @license URL [licensename]: to display a hyperlink to a URL for a license
     * @var string|null
     */
    public string|null $License = null;

    public array $Modifires = [];
    public mixed $DefaultValue = null;
    public mixed $Value = null;
    public string|null $Comment = null;
    public mixed $Path = null;


    public function __construct($reflection=null, $object = null){
        $this->Set($reflection);
    }

    public function Set($reflection, $object = null){
        if(!is_null($object) && is_null($reflection))
            if(is_subclass_of($object, "\Base")) $reflection = new \ReflectionClass($object);
            elseif(is_object($object)) $reflection = new \ReflectionObject($object);
        if(!is_null($reflection))
            if($reflection instanceof \ReflectionClass)
                $this->SetClass($reflection, $object);
            elseif($reflection instanceof \ReflectionObject)
                $this->SetObject($reflection, $object);
            elseif($reflection instanceof \ReflectionMethod)
                $this->SetMethod($reflection, $object);
            elseif($reflection instanceof \ReflectionFunction)
                $this->SetFunction($reflection, $object);
            elseif($reflection instanceof \ReflectionProperty)
                $this->SetProperty($reflection, $object);
            elseif($reflection instanceof \ReflectionAttribute)
                $this->SetAttribute($reflection, $object);
            elseif($reflection instanceof \ReflectionGenerator)
                $this->SetGenerator($reflection, $object);
            else $this->SetObject(new ReflectionObject($reflection, $object));
    }
    public function SetClass(\ReflectionClass $reflection, $object = null){
        $this->Type = "class";
        $this->Name = $reflection->getName();
        $this->Path = $reflection->getFileName();
        $this->Load($reflection->getDocComment());
        if(is_null($object)) foreach ($reflection->getProperties() as $value) $this[$value->getName()] = new Reflected($value);
        else foreach ($reflection->getProperties() as $value) $this[$value->getName()] = new Reflected($value, $value->getValue($object));
    }
    public function SetObject(\ReflectionObject $reflection, $object = null){
        $this->Type = "object";
        $this->Name = $reflection->getName();
        $this->Path = $reflection->getFileName();
        $this->Load($reflection->getDocComment());
        if(is_null($object)) foreach ($reflection->getProperties() as $value) $this[$value->getName()] = new Reflected($value);
        else foreach ($reflection->getProperties() as $value) $this[$value->getName()] = new Reflected($value, $value->getValue($object));
    }
    public function SetProperty(\ReflectionProperty $reflection, $object = null){
        $this->Type = "property";
        $this->Name = $reflection->getName();
        $this->DefaultValue = $reflection->getDefaultValue();
        $this->Modifires = [];
        if(!is_null($object)) {
            $this->Value = $reflection->getValue($object);
            $this->Var = $reflection->getType();
            $this->Var = $this->Var === null?null:$this->Var."";
            if($reflection->isPublic) $this->Modifires[] = "public";
            if($reflection->isPrivate) $this->Modifires[] = "private";
            if($reflection->isProtected) $this->Modifires[] = "protected";
            if($reflection->isReadOnly) $this->Modifires[] = "readonly";
            if($reflection->isStatic) $this->Modifires[] = "static";
        }
        $this->Load($reflection->getDocComment());
    }
    public function SetMethod(\ReflectionMethod $reflection, $object = null){
        $this->Type = "method";
        $this->Name = $reflection->getName();
        $this->Modifires = [];
        if($reflection->isPublic) $this->Modifires[] = "public";
        if($reflection->isPrivate) $this->Modifires[] = "private";
        if($reflection->isProtected) $this->Modifires[] = "protected";
        if($reflection->isStatic) $this->Modifires[] = "static";
        $this->Load($reflection->getDocComment());
    }
    public function SetFunction(\ReflectionFunction $reflection, $object = null){
        $this->Type = "function";
        $this->Name = $reflection->getName();
        $this->Load($reflection->getDocComment());
    }
    public function SetGenerator(\ReflectionGenerator $reflection, $object = null){
        $this->Type = "generator";
        $this->Name = $reflection->getName();
        $this->Load($reflection->getDocComment());
    }
    public function SetAttribute(\ReflectionAttribute $reflection, $object = null){
        $this->Type = "attribute";
        $this->Name = $reflection->getName();
        $this->Load($reflection->getDocComment());
    }

    public function Load($comment){
        $this->Comment = $comment;
        $comments = Reflect::CommentParameters($comment);
        $splt = "\t \r\n\f\v";
        $this->Title = getValid($comments, "title")??Convert::ToTitle($this->Name);
        $this->Description = getValid($comments, "description")??getValid($comments, "abstract");
        $this->Var = is_null($this->Var)||$this->Var=="mixed"?getValid($comments, "var", $this->Var??"mixed"):$this->Var;
        $this->Vars = preg_split("/\s*\|\s*/", $this->Var);
        $size = getValid($comments, "size");
        switch ($size) {
        	case "sm":
        	case "small":
                $this->Size = 0.1;
                break;
        	case "md":
        	case "medium":
                $this->Size = 0.5;
                break;
        	case "lg":
        	case "large":
                $this->Size = 1;
                break;
        	default:
                $this->Size = (float)$size;
                break;
        }
        $this->Category = doValid(function($v)use($splt){ return explode($splt, $v)[0];}, $comments, "category");
        $this->Visible = !isset($comments["internal"]);
        $this->Access = doValid(function($v)use($splt){ return explode($splt, $v)[0];},$comments, "access");
        $this->Version = doValid(function($v)use($splt){ return explode($splt, $v)[0];},$comments, "version");
        $this->Example = getValid($comments, "example");
        $this->Link = getValid($comments, "link");
        $this->See = getValid($comments, "see");
        $this->Author = getValid($comments, "author");
        if(!is_null($this->Author)) $this->Authors = preg_split("/\s+\;\s+/", $this->Author);
        $this->Copyright = getValid($comments, "copyright");
        $this->License = getValid($comments, "license");
    }
}
?>