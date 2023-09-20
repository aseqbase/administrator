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
    /**
     * To get each Features of a Class
     * @param mixed $object
     */
    public static function WriteClass($path, ReflectedThing $source){
        $content = file_get_contents($path);
        foreach ($source as $name => $prop) {
            $start = preg_find("/(\s*class[\s\b]+\w+[\s\b]*[\w\W]*\{[\w\W]+\s+)(?=\$$name\W)/", $content);
            if($start) $content = $start.preg_replace("/^\$${$name}[^;]*(?:(?:(\"|')[\W\w]*\1[^;]*)|(?:[^;\"']*))*;/U",
                    "\$$name = ".Convert::ToValue($prop->Value,$prop->Vars),
                    substr($content, strlen($start)));
            else {
                $start = preg_find("/(\s*class[\s\b]+\w+[\s\b]*[\w\W]*\{\s*/", $content);
                if($start) $content = $start.preg_replace("/^\$${$name}[^;]*(?:(?:(\"|')[\W\w]*\1[^;]*)|(?:[^;\"']*))*;/U",
                 "\$$name = ".Convert::ToValue($prop->Value,$prop->Vars),
                 substr($content, strlen($start)));
                else throw new \Exception("There is not any class named '$name'!");
            }
        }
        return file_put_contents($path, $content);
    }
    /**
     * To get each Features of a property
     * @param mixed $object
     */
    public static function SetProperty($objectOrPath, ReflectedThing $source){
        $ref = new ReflectedThing();
        $ref->Type = "[\w\<\>\|]+";
        $name = "\w+";
        $type = "[\w\<\>\|]+";
        $body = "(?<=\=)[\w\W]+\;\s*\r\n";
        if(is_object($objectOrPath)) return new ReflectedThing($objectOrPath);
        elseif(file_exists($objectOrPath)) $objectOrPath = file_get_contents($objectOrPath);
        else return new ReflectedThing(new \ReflectionObject($objectOrPath));
        foreach (preg_find_all("/(?<=\s)*({$type})(\s|\b)+({$name})(\s|\b)*[\w\W]*({$body})/i", $objectOrPath) as $part)
        {
            $ref->Name = preg_find("/{$name}/", $part);
            $thing = new ReflectedThing();
            $ref = $thing;
        }
        return $ref;
    }

    public static function CommentParameters(string|null $comment){
        if(is_null($comment)) return [];
        $matches = preg_find_all("#(@[a-zA-Z]+\s*.*)#", $comment);
        $res = [];
        $res["abstract"] = Convert::ToString(preg_find_all("/(?<=\*\s+)[a-zA-Z].*/", $comment));
        foreach ($matches as $value)
            $res[preg_find("/(?<=\@)[a-zA-Z]+/", $value)] = preg_find("/(?<=\@[a-zA-Z]+\s+).*/", $value);
        return $res;
    }

    /**
     * To get each Features of a Class
     * @param mixed $object
     */
    public static function Class($object){
        return new ReflectedThing(new \ReflectionClass($object));
    }
    /**
     * To get each Features of an Object
     * @param mixed $object
     */
    public static function Object($object){
        return new ReflectedThing(new \ReflectionObject($object));
    }
    ///**
    // * To get each Features of an Enum
    // * @param mixed $object
    // */
    //public static function Enum($object){
    //    $ref = new \ReflectionEnum($object);
    //    return $ref;
    //}
}

class ReflectedThing extends \ArrayObject{
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


    public function __construct($reflected=null){
        $this->Set($reflected);
    }

    public function Set($reflected){
        if(!is_null($reflected))
            if($reflected instanceof \ReflectionClass)
                $this->SetClass($reflected);
            elseif($reflected instanceof \ReflectionObject)
                $this->SetObject($reflected);
            elseif($reflected instanceof \ReflectionMethod)
                $this->SetMethod($reflected);
            elseif($reflected instanceof \ReflectionFunction)
                $this->SetFunction($reflected);
            elseif($reflected instanceof \ReflectionProperty)
                $this->SetProperty($reflected);
            elseif($reflected instanceof \ReflectionAttribute)
                $this->SetAttribute($reflected);
            elseif($reflected instanceof \ReflectionGenerator)
                $this->SetGenerator($reflected);
            else $this->SetObject(new ReflectionObject($reflected));
    }
    public function SetClass(\ReflectionClass $reflected){
        $this->Type = "class";
        $this->Name = $reflected->getName();
        $this->Load($reflected->getDocComment());
        foreach ($reflected->getProperties() as $value) $this[$value->getName()] = new ReflectedThing($value);
    }
    public function SetObject(\ReflectionObject $reflected){
        $this->Type = "object";
        $this->Name = $reflected->getName();
        $this->Load($reflected->getDocComment());
        foreach ($reflected->getProperties() as $value) $this[$value->getName()] = new ReflectedThing($value);
    }
    public function SetProperty(\ReflectionProperty $reflected){
        $this->Type = "property";
        $this->Name = $reflected->getName();
        $this->DefaultValue = $reflected->getDefaultValue();
        $this->Value = $reflected->getValue();
        $this->Var = $reflected->getType();
        $this->Modifires = [];
        if($reflected->isPublic) $this->Modifires[] = "public";
        if($reflected->isPrivate) $this->Modifires[] = "private";
        if($reflected->isProtected) $this->Modifires[] = "protected";
        if($reflected->isReadOnly) $this->Modifires[] = "readonly";
        if($reflected->isStatic) $this->Modifires[] = "static";
        $this->Load($reflected->getDocComment());
    }
    public function SetMethod(\ReflectionMethod $reflected){
        $this->Type = "method";
        $this->Name = $reflected->getName();
        $this->Modifires = [];
        if($reflected->isPublic) $this->Modifires[] = "public";
        if($reflected->isPrivate) $this->Modifires[] = "private";
        if($reflected->isProtected) $this->Modifires[] = "protected";
        if($reflected->isStatic) $this->Modifires[] = "static";
        $this->Load($reflected->getDocComment());
    }
    public function SetFunction(\ReflectionFunction $reflected){
        $this->Type = "function";
        $this->Name = $reflected->getName();
        $this->Load($reflected->getDocComment());
    }
    public function SetGenerator(\ReflectionGenerator $reflected){
        $this->Type = "generator";
        $this->Name = $reflected->getName();
        $this->Load($reflected->getDocComment());
    }
    public function SetAttribute(\ReflectionAttribute $reflected){
        $this->Type = "attribute";
        $this->Name = $reflected->getName();
        $this->Load($reflected->getDocComment());
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